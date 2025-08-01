<?php

namespace App\Repositories;

use App\Http\Traits\ApprovalTrackerTrait;
use App\Models\Notification;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class CoverRepositoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ApprovalNotificationRepository extends BaseRepository
{

    use ApprovalTrackerTrait;

    public function __construct() {}

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Notification::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
        }
    }
}
