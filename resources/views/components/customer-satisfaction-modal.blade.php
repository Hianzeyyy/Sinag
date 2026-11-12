<div class="modal fade" id="customerSatisfactionModal" tabindex="-1" aria-labelledby="customerSatisfactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <div>
                    <h2 class="modal-title h5 fw-bold" id="customerSatisfactionModalLabel">Rate the SINAG system</h2>
                    <p class="text-muted small mb-0">Your feedback helps us improve this service.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('customer-satisfaction.store') }}">
                @csrf
                <div class="modal-body pt-0">
                    <fieldset class="mb-3">
                        <legend class="form-label fw-semibold">How satisfied are you with this system?</legend>
                        <div class="d-flex gap-2" role="radiogroup" aria-label="System rating">
                            @for($rating = 1; $rating <= 5; $rating++)
                                <label class="csat-star-label" title="{{ $rating }} star{{ $rating > 1 ? 's' : '' }}">
                                    <input type="radio" name="rating" value="{{ $rating }}" class="visually-hidden csat-star-input" required>
                                    <i class="bi bi-star-fill csat-star" aria-hidden="true"></i>
                                    <span class="visually-hidden">{{ $rating }} star</span>
                                </label>
                            @endfor
                        </div>
                    </fieldset>
                    @php
                        $surveyQuestions = [
                            'The report or suggestion submission process was easy to use.',
                            'The instructions and information were clear.',
                            'The system made me feel safe sharing my concern or feedback.',
                            'The system responded well to my needs.',
                            'I would recommend this system to others who need support.',
                        ];
                        $surveyChoices = [
                            1 => 'Strongly disagree',
                            2 => 'Disagree',
                            3 => 'Agree',
                            4 => 'Strongly agree',
                        ];
                    @endphp
                    <fieldset class="mb-3">
                        <legend class="form-label fw-semibold">Customer satisfaction survey</legend>
                        @foreach($surveyQuestions as $questionIndex => $question)
                            <div class="mb-3">
                                <p class="small fw-semibold mb-2">{{ $questionIndex + 1 }}. {{ $question }}</p>
                                <div class="d-grid gap-2">
                                    @foreach($surveyChoices as $choiceValue => $choiceLabel)
                                        <label class="survey-choice">
                                            <input type="radio" name="responses[{{ $questionIndex }}]" value="{{ $choiceValue }}" required>
                                            <span>{{ $choiceLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </fieldset>
                    <label for="customerSatisfactionFeedback" class="form-label fw-semibold">Additional feedback</label>
                    <textarea id="customerSatisfactionFeedback" name="feedback" class="form-control" rows="4" maxlength="2000" placeholder="What can we improve? (Optional)"></textarea>
                    <p class="small text-muted mt-2 mb-0">Your submitted survey is recorded once and will not be asked again.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Maybe later</button>
                    <button type="submit" class="btn rounded-pill px-4 text-white" style="background:#8f72f5;">Submit feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .csat-star-label { cursor: pointer; color: #d1d5db; font-size: 2rem; line-height: 1; }
    .csat-star-label.is-selected { color: #facc15; }
    .csat-star-input:checked + .csat-star { color: #facc15; }
    .survey-choice { display:flex; align-items:center; gap:.55rem; padding:.55rem .7rem; border:1px solid #e5e7eb; border-radius:.65rem; cursor:pointer; }
    .survey-choice:has(input:checked) { color:#4c2a9d; background:#f1eaff; border-color:#a594f9; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('customerSatisfactionModal');
        if (!modal) return;
        const labels = [...modal.querySelectorAll('.csat-star-label')];
        function paintStars(index) {
            labels.forEach((item, itemIndex) => item.classList.toggle('is-selected', itemIndex <= index));
        }
        labels.forEach((label, index) => {
            label.addEventListener('mouseenter', () => paintStars(index));
            label.addEventListener('click', () => paintStars(index));
        });
        modal.querySelector('[role="radiogroup"]')?.addEventListener('mouseleave', function () {
            const selected = labels.findIndex(label => label.querySelector('input:checked'));
            labels.forEach((item, itemIndex) => item.classList.toggle('is-selected', selected >= 0 && itemIndex <= selected));
        });
        window.addEventListener('load', function () {
            if (window.bootstrap?.Modal) {
                bootstrap.Modal.getOrCreateInstance(modal).show();
            }
        }, { once: true });
    });
</script>
