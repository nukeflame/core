<?php

namespace App\Http\Controllers;

use App\Models\CoverRegister;
use App\Models\CoverRisk;
use App\Models\ScheduleHeader;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
// use Mews\Purifier\Facades\Purifier;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Symfony\Component\DomCrawler\Crawler;

class CoverRiskController extends Controller
{
    private function normalizeScheduleKey(string $value): string
    {
        return strtolower((string) preg_replace('/[^a-z0-9]+/', '', $value));
    }

    private function createTableHtml($items)
    {
        $tableHtml = '<table style="width: 100%; border-collapse: collapse; font-family: inherit; font-size: inherit; margin: 10px 0;">';
        $rowOpen = false;
        foreach ($items as $index => $item) {
            if ($index % 2 == 0) {
                $tableHtml .= '<tr>';
                $rowOpen = true;
            }
            $tableHtml .= '<td style="border: none; width: 100%; padding: 8px; text-align: left;">' .
                htmlspecialchars($item['item']) .
                '</td>';
            if ($index % 2 == 1) {
                $tableHtml .= '</tr>';
                $rowOpen = false;
            }
        }
        if ($rowOpen) {
            $tableHtml .= '<td style="border: none; padding: 8px;"></td></tr>';
        }
        $tableHtml .= '</table>';

        return $tableHtml;
    }

    public function add_schedule(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'header' => 'required',
                'cover_no' => 'required|string|max:20',
                'endorsement_no' => 'required|string|max:20',
                'title' => 'required|string',
                'schedule_position' => 'nullable|int',
                'description' => 'nullable|string|max:255',
                'details' => 'nullable',
            ]);

            $schedule_details = [];
            $details = $validatedData['details'];
            if (!empty($details) && is_string($details) && (
                strpos($details, '<table') !== false ||
                strpos($details, '<tbody') !== false ||
                strpos($details, '<tr') !== false
            )) {
                $crawler = new Crawler($details);
                if ($crawler->filter('td')->count() > 0) {
                    $crawler->filter('td')->each(function (Crawler $node) use (&$schedule_details) {
                        $schedule_name = trim($node->text());
                        if (!empty($schedule_name)) {
                            $schedule_details[] = ['item' => $schedule_name];
                        }
                    });
                    $tableHtml = $this->createTableHtml($schedule_details);
                } else {
                    $tableHtml = $details;
                }
            } else {
                $tableHtml = $details;
            }

            $id = (int) CoverRisk::where('endorsement_no', $validatedData['endorsement_no'])->withTrashed()->max('id') + 1;
            $username = Auth::user()->user_name;
            $validatedData['id'] = $id;
            $validatedData['created_by'] = $username;
            $validatedData['updated_by'] = $username;
            $validatedData['sum_insured'] = (float)str_replace(',', '', $request->schedule_value) ?? 0;
            $validatedData['premium'] = (float)str_replace(',', '', $request->premium) ?? 0;
            $validatedData['details'] = $tableHtml;
            $validatedData['description'] = trim((string) ($validatedData['description'] ?? '')) ?: null;
            // $validatedData['details'] = Purifier::clean($validatedData['details']);
            $validatedData['schedule_position'] = (int) $validatedData['schedule_position'];

            CoverRisk::create($validatedData);
            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully'
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function amend_schedule(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'id' => 'required',
                'header' => 'required',
                'cover_no' => 'required|string|max:20',
                'endorsement_no' => 'required|string|max:20',
                'title' => 'required|string|max:100',
                'description' => 'nullable|string|max:255',
            ]);

            $coverRisk = CoverRisk::where('endorsement_no', $request->endorsement_no)
                ->where('id', $request->id)
                ->first();

            $coverRisk->title = $request->title;
            $coverRisk->header = $request->header;
            $coverRisk->details = $request->details;
            $coverRisk->schedule_position = (int) $request->schedule_position;
            $coverRisk->description = trim((string) ($request->description ?? '')) ?: null;
            $coverRisk->sum_insured = (float)str_replace(',', '', $request->schedule_value) ?? 0;
            $coverRisk->premium = (float)str_replace(',', '', $request->premium) ?? 0;
            $coverRisk->updated_by = Auth::user()->user_name;
            $coverRisk->save();

            DB::commit();
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully'
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete_schedule(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'cover_no' => 'required|string|max:20',
                'endorsement_no' => 'required|string|max:20',
            ]);

            $coverRisk = CoverRisk::where('endorsement_no', $request->endorsement_no)
                ->where('id', $request->id)
                ->first();
            $coverRisk->forceDelete();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Schedule item removed successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to remove schedule item'
            ]);
        }
    }

    public function get_schedule(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'endorsement_no' => 'required|string|max:20',
            ]);

            $schedule = CoverRisk::query()
                ->where('endorsement_no', $validated['endorsement_no'])
                ->where('id', $validated['id'])
                ->first();

            if (!$schedule) {
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => 'Schedule not found',
                ], 404);
            }

            return response()->json([
                'status' => Response::HTTP_OK,
                'data' => [
                    'id' => $schedule->id,
                    'header' => $schedule->header,
                    'title' => $schedule->title,
                    'schedule_position' => $schedule->schedule_position,
                    'description' => $schedule->description,
                    'details' => $schedule->details,
                    'cover_no' => $schedule->cover_no,
                    'endorsement_no' => $schedule->endorsement_no,
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function available_schedule_items(Request $request)
    {
        try {
            $validated = $request->validate([
                'endorsement_no' => 'required|string|max:20',
                'include_header_id' => 'nullable|integer',
            ]);

            $endorsementNo = $validated['endorsement_no'];
            $includeHeaderId = (int) ($validated['include_header_id'] ?? 0);

            $cover = CoverRegister::query()
                ->where('endorsement_no', $endorsementNo)
                ->select(['prospect_id'])
                ->first();

            $opportunityId = trim((string) ($cover?->prospect_id ?? ''));
            if ($opportunityId === '') {
                return response()->json([
                    'status' => Response::HTTP_OK,
                    'data' => [],
                ]);
            }

            $usedHeaderIds = CoverRisk::withTrashed()
                ->where('endorsement_no', $endorsementNo)
                ->pluck('header')
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            $query = ScheduleHeader::query()
                ->where('opportunity_id', $opportunityId)
                ->select(['id', 'name', 'position', 'schedule_header_id']);

            if (!empty($usedHeaderIds) || $includeHeaderId > 0) {
                $query->where(function ($builder) use ($usedHeaderIds, $includeHeaderId) {
                    if (!empty($usedHeaderIds)) {
                        $builder->whereNotIn('id', $usedHeaderIds);
                    }

                    if ($includeHeaderId > 0) {
                        $builder->orWhere('id', $includeHeaderId);
                    }
                });
            }

            $items = $query
                ->orderBy('position')
                ->orderBy('name')
                ->get()
                ->filter(function ($item) {
                    $excluded = ['suminsured', 'premium', 'deductibleexcess'];
                    $key = $this->normalizeScheduleKey((string) ($item->name ?? ''));
                    return !in_array($key, $excluded, true);
                })
                ->values();

            $lookupIds = $items
                ->flatMap(function ($item) {
                    return [
                        (int) ($item->id ?? 0),
                        (int) ($item->schedule_header_id ?? 0),
                    ];
                })
                ->filter(fn($id) => $id > 0)
                ->unique()
                ->values();

            $termsByHeader = collect();
            if ($lookupIds->isNotEmpty()) {
                $termsByHeader = DB::table('bd_terms_conditions')
                    ->where('opportunity_id', $opportunityId)
                    ->whereIn('schedule_header_id', $lookupIds->all())
                    ->select(['schedule_header_id', 'content'])
                    ->get()
                    ->keyBy(fn($row) => (int) $row->schedule_header_id);
            }

            $items = $items->map(function ($item) use ($termsByHeader) {
                $primaryKey = (int) ($item->schedule_header_id ?? 0);
                $fallbackKey = (int) ($item->id ?? 0);

                $item->content = $termsByHeader->get($primaryKey)?->content
                    ?? $termsByHeader->get($fallbackKey)?->content
                    ?? '';

                return $item;
            });

            return response()->json([
                'status' => Response::HTTP_OK,
                'data' => $items,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
