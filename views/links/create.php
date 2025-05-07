<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="content-wrapper vertically-center">
    <div class="container content">
        <div class="row">
            <div class="col-lg-12">

                <div class="content-container form-content shadow-sm rounded">
                    <h3 class="pt-3 pl-4 pb-3">Create a Link</h3>

                    <form action="/create" method="POST" class="clearfix createForm create-edit-form">

                        <div class="form-group">
                            <label>Link Name</label>

                            <div class="perc-click-button-div">
                                <p class="mr-2">
                                    Switch to <span class="perc-click-span-createForm">Clicks</span> Mode
                                    <i class="form-tooltip mr-2 ml-1" title="Percentage/Click Mode. Default: Percentage">?</i>
                                </p>

                                <input id="perc-click-createForm-input" type="checkbox" name="isClickCheckbox">

                                <label for="perc-click-createForm-input" class="check-trail">
                                    <span class="check-handler"></span>
                                </label>
                            </div>

                            <input type="text" class="form-control name-input" name="name" placeholder="eg. campaign one..." autocomplete="off" required>

                        </div>

                        <div class="form-group default-page-div" >
                            <label>Default Destination</label>
                            <i class="form-tooltip mr-2 ml-1" title="Traffic will be redirected to this page after all click counts are filled.">?</i>

                            <input type="url" class="form-control default-page-input" name="defaultUrl" value="https://" placeholder="default destination..." autocomplete="off" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-9 mb-0">
                                <label>Redirects To</label>
                            </div>
                            <div class="form-group col-3 mb-0">
                                <label>
                                    <span class="mr-1 perc-click-span-createForm-second">Percent (%)</span>
                                </label>
                            </div>
                        </div>

                        <div class="urls-container">
                            <div class="regular-urls">
                                <div class="form-row url-pair">
                                    <div class="form-group col-2 mb-2">
                                        <input class="form-control destination-num" type="text" disabled value="1">
                                    </div>
                                    <div class="form-group col-7 mb-2">
                                        <input class="form-control destination-input" type="url" name="url[]" value="https://" placeholder="destination url..." autocomplete="off" >
                                    </div>
                                    <div class="form-group col-3 mb-2 position-relative">
                                        <input class="form-control percent-input" type="number" name="perc[]" autocomplete="off" placeholder="%" max="100" min="0" >

                                        <button type="button" class="btn btn-danger delete-btn position-absolute">
                                            <ion-icon name="close-outline"></ion-icon>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-row url-pair">
                                    <div class="form-group col-2 mb-2">
                                        <input class="form-control destination-num" type="text" disabled value="2">
                                    </div>
                                    <div class="form-group col-7 mb-2">
                                        <input class="form-control destination-input" type="url" name="url[]" value="https://" placeholder="destination url..." autocomplete="off" >
                                    </div>
                                    <div class="form-group col-3 mb-2 position-relative">
                                        <input class="form-control percent-input" type="number" name="perc[]" autocomplete="off" placeholder="%" max="100" min="0">

                                        <button type="button" class="btn btn-danger delete-btn position-absolute">
                                            <ion-icon name="close-outline"></ion-icon>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="os-specific-urls" style="display:none;">

                            </div>
                        </div>

                        <div class="overflow-hidden py-1">
                            <div class="form-group float-left mb-0">
                                <button type="button" class="btn btn-secondary d-inline-flex align-items-center addFieldBtn">
                                    <ion-icon name="add-outline" class="mr-1"></ion-icon>Add Field
                                </button>
                            </div>

                            <div class="form-group float-right mb-0 auto-man-button-div">
                                <p class="mr-2">
                                    <span class="mr-2 auto-man-span-createForm">Manual</span><i class="form-tooltip mr-2" title="Manual/Auto Percentage Allocation. Default: Auto">?</i>
                                </p>

                                <input id="auto-man-createForm-input" type="checkbox">

                                <label for="auto-man-createForm-input" class="check-trail">
                                    <span class="check-handler"></span>
                                </label>
                            </div>
                        </div>
                        <div class="d-flex" style="flex-direction: column;align-items: flex-end;gap: 8px;">
                            <div class="form-group float-right mb-0 equal-dist-button-div">
                                <p class="mr-2">
                                    <span class="mr-2 equal-dist-span-createForm">Equal</span>
                                    <i class="form-tooltip mr-2" title="Enable equal distribution among all destinations (overrides percentages)">?</i>
                                </p>
                                <input id="equal-dist-createForm-input" type="checkbox" name="is_equal_distribution">
                                <label for="equal-dist-createForm-input" class="check-trail">
                                    <span class="check-handler"></span>
                                </label>
                            </div>
                            <div class="form-group float-right mb-0 os-filter-button-div">
                                <p class="mr-2">
                                    <span class="mr-2 os-filter-span-createForm">OS Filter</span>
                                    <i class="form-tooltip mr-2" title="Enable OS-specific URL routing">?</i>
                                </p>
                                <input id="os-filter-createForm-input" type="checkbox" name="os_filter_enabled">
                                <label for="os-filter-createForm-input" class="check-trail">
                                    <span class="check-handler"></span>
                                </label>
                            </div>
                        </div>
                        <hr style="height: 1px; background-color: var(--background-tertiary);">
                        <p class="error-message float-left"></p>

                        <button type="submit" class="btn btn-primary float-right py-1">Create</button>
                        <button type="button" class="btn btn-secondary float-right py-1 mr-2 text-white reset-btn">Reset</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>