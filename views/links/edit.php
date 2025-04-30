<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="content-wrapper vertically-center">
    <div class="container content">
        <div class="row">
            <div class="col-lg-12">

                <div class="content-container form-content shadow-sm rounded">
                    <h3 class="pt-3 pl-4 pb-3">Edit <span><?php echo isset($link['name']) ? htmlspecialchars($link['name']) : ''; ?></span></h3>

                    <form method="POST" class="clearfix editForm create-edit-form" action="/edit/<?php echo isset($link['short']) ? htmlspecialchars($link['short']) : ''; ?>?_method=PUT">

                        <div class="form-group">
                            <label>Link Name</label>

                            <div class="perc-click-button-div">
                                <p class="mr-2">
                                    Switch to <span class="perc-click-span-editForm"><?php echo ($link['is_click']) ? 'Percentage' : 'Clicks'; ?></span> Mode
                                    <i class="form-tooltip mr-2 ml-1" title="Percentage/Click Mode. Default: Percentage">?</i>
                                </p>

                                <input id="perc-click-edit-input" type="checkbox" name="isClickCheckbox" <?php echo ($link['is_click']) ? 'checked' : ''; ?>>

                                <label for="perc-click-edit-input" class="check-trail">
                                    <span class="check-handler"></span>
                                </label>
                            </div>

                            <input type="text" class="form-control name-input" name="name" value="<?php echo isset($link['name']) ? htmlspecialchars($link['name']) : ''; ?>" placeholder="eg. campaign one..." autocomplete="off" required>
                        </div>

                        <div class="form-group default-page-div" style="<?php echo ($link['is_click']) ? 'display:block' : 'display:none'; ?>">
                            <label>Default Destination</label>
                            <i class="form-tooltip mr-2 ml-1" title="Traffic will be redirected to this page after all click counts are filled.">?</i>

                            <input type="url" class="form-control default-page-input" name="defaultUrl" value="<?php echo isset($link['default_destination']['url']) ? htmlspecialchars($link['default_destination']['url']) : 'https://'; ?>" placeholder="default destination..." autocomplete="off" required>
                        </div>



                        <div class="form-row">
                            <div class="form-group col-9 mb-0">
                                <label>Redirects To</label>
                            </div>
                            <div class="form-group col-3 mb-0">
                                <label>
                                    <span class="mr-1 perc-click-span-editForm-second">
                                        <?php echo ($link['is_click']) ? 'Clicks' : 'Percent (%)'; ?>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="urls-container">
                            <?php if (isset($link['full'])) : ?>
                                <?php foreach ($link['full'] as $index => $item) : ?>
                                    <div class="form-row url-pair">
                                        <div class="form-group col-2 mb-2">
                                            <input class="form-control destination-num" type="text" disabled value="<?php echo $index + 1; ?>">
                                        </div>
                                        <div class="form-group col-7 mb-2">
                                            <input class="form-control destination-input" type="url" name="url[]" value="<?php echo htmlspecialchars($item['url']); ?>" placeholder="destination url..." autocomplete="off" required>
                                        </div>
                                        <div class="form-group col-3 mb-2 position-relative">
                                            <input class="form-control percent-input" type="number"
                                                name="<?php echo (isset($item['perc']) && $item['perc'] === null) ? 'clicks[]' : 'perc[]'; ?>"
                                                value="<?php echo (isset($item['perc']) && $item['perc'] !== null) ? $item['perc'] : (isset($item['clicks']) ? $item['clicks'] : ''); ?>"
                                                autocomplete="off"
                                                placeholder="<?php echo (isset($item['perc']) && $item['perc'] === null) ? 'clicks..' : '%'; ?>"
                                                required>
                                            <button type="button" class="btn btn-danger delete-btn position-absolute">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="overflow-hidden py-1">
                            <div class="form-group float-left mb-0">
                                <button type="button" class="btn btn-secondary d-inline-flex align-items-center addFieldBtn">
                                    <ion-icon name="add-outline" class="mr-1"></ion-icon>Add Field
                                </button>
                            </div>

                            <div class="form-group float-right mb-0 auto-man-button-div">
                                <p class="mr-2">
                                    <span class="mr-2 auto-man-span-editForm">Auto</span><i class="form-tooltip mr-2" title="Manual/Auto Percentage Allocation. Default: Auto">?</i>
                                </p>

                                <input id="auto-man-edit-input" type="checkbox">

                                <label for="auto-man-edit-input" class="check-trail">
                                    <span class="check-handler"></span>
                                </label>
                            </div>
                        </div>

                        <hr class="my-3">
                        <p class="error-message float-left"></p>

                        <button type="submit" class="btn btn-primary float-right py-1">Update</button>
                        <button type="button" class="btn btn-secondary float-right py-1 mr-2 text-white reset-btn">Reset</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</main>

<script type="text/javascript">
    // JavaScript remains the same as it will be handled client-side
    document.addEventListener('DOMContentLoaded', function() {
        const percClickCheckbox = document.querySelector('main .form-content input[type=checkbox]');
        const percClickSpan = document.querySelector('main .form-content .perc-click-span-editForm');
        const percClickSpan2 = document.querySelector('main .form-content .perc-click-span-editForm-second');
        const defaultDiv = document.querySelector('main .form-content .default-page-div');

        if (percClickCheckbox) {
            percClickCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    percClickSpan.textContent = "Percentage";
                    percClickSpan2.textContent = "Clicks";
                    defaultDiv.style.display = "block";
                } else {
                    percClickSpan.textContent = "Clicks";
                    percClickSpan2.textContent = "Percent (%)";
                    defaultDiv.style.display = "none";
                }
            });
        }
    });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>