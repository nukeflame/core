<?php

namespace App\Http\Controllers;

use App\Models\CoverRisk;
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
            ]);

            $coverRisk = CoverRisk::where('endorsement_no', $request->endorsement_no)
                ->where('id', $request->id)
                ->first();

            $coverRisk->title = $request->title;
            $coverRisk->header = $request->header;
            $coverRisk->details = $request->details;
            $coverRisk->schedule_position = (int) $request->schedule_position;
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
}
