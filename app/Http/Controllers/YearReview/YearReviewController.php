<?php

namespace App\Http\Controllers\YearReview;

use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Utility\PaginateRequest;
use App\Models\YearReview;
use App\Shared\Fields\Fields;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class YearReviewController extends Controller
{
    use HttpResponse;

    public function getReviews(PaginateRequest $request)
    {
        $userId = $request->attributes->get('userId');
        $reviews = YearReview::query()
            ->availableTo($userId)
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->select('year', 'review', 'viewed')
            ->paginate($request->query(Fields::PER_PAGE, config('app.per_page_default')));

        $items = $reviews->getCollection()
            ->map(fn (YearReview $review): array => $this->preview($review))
            ->values()->all();

        return $this->success(
            $this->paginator(
                [Fields::ITEMS => $items],
                $reviews->total(),
                $reviews->perPage(),
                $reviews->currentPage()
            )
        );
    }

    public function getReviewByYear(Request $request, int $year)
    {
        $userId = $request->attributes->get('userId');
        $reviewObj = YearReview::query()
            ->availableTo($userId)
            ->where('year', $year)
            ->orderByDesc('id')
            ->first();

        if ($reviewObj == null) {
            throw new NotFoundException('Отчет года не найден');
        }

        if (! $reviewObj->viewed) {
            $reviewObj->viewed = true;
            $reviewObj->save();
        }

        return $this->success($reviewObj->review);
    }

    public function getNewReview(Request $request)
    {
        $userId = $request->attributes->get('userId');
        $reviewObj = YearReview::query()
            ->availableTo($userId)
            ->where('viewed', false)
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->first();

        if ($reviewObj == null) {
            return $this->success(null);
        }

        return $this->success($this->preview($reviewObj));
    }

    private function preview(YearReview $review): array
    {
        return [
            ...($review->review['pages'][0] ?? []),
            'year' => $review->year,
            'viewed' => $review->viewed,
        ];
    }
}
