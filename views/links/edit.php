<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="content-wrapper vertically-center">
    <div class="container content">
        <div class="row">
            <div class="col-lg-12">
                <div class="content-container form-content shadow-sm rounded">
                    <h3 class="pt-3 pl-4 pb-3">Edit <span><?= htmlspecialchars($link['name'] ?? '') ?></span></h3>

                    <form method="POST" class="clearfix editForm create-edit-form" action="/edit/<?= htmlspecialchars($link['short'] ?? '') ?>?_method=PUT">

                        <!-- Link Name and Mode Toggle -->
                        <div class="form-group">
                            <label>Link Name</label>
                            <div class="perc-click-button-div">
                                <p class="mr-2">
                                    Switch to <span class="perc-click-span-editForm"><?= ($link['is_click'] ?? false) ? 'Percentage' : 'Clicks' ?></span> Mode
                                    <i class="form-tooltip mr-2 ml-1" title="Percentage/Click Mode. Default: Percentage">?</i>
                                </p>
                                <input id="perc-click-edit-input" type="checkbox" name="isClickCheckbox" <?= ($link['is_click'] ?? false) ? 'checked' : '' ?>>
                                <label for="perc-click-edit-input" class="check-trail">
                                    <span class="check-handler"></span>
                                </label>
                            </div>
                            <input type="text" class="form-control name-input" name="name"
                                value="<?= htmlspecialchars($link['name'] ?? '') ?>"
                                placeholder="eg. campaign one..." autocomplete="off" required>
                        </div>

                        <!-- Default Destination -->
                        <div class="form-group default-page-div" style="<?= ($link['is_click'] ?? false) ? 'display:block' : 'display:none'; ?>">
                            <label>Default Destination</label>
                            <i class="form-tooltip mr-2 ml-1" title="Traffic will be redirected to this page after all click counts are filled.">?</i>
                            <input type="url" class="form-control default-page-input" name="defaultUrl"
                                value="<?= htmlspecialchars($link['default_destination']['url'] ?? 'https://') ?>"
                                placeholder="default destination..." autocomplete="off" required>
                        </div>

                        <!-- Redirects Header -->
                        <div class="form-row">
                            <div class="form-group col-9 mb-0">
                                <label>Redirects To</label>
                            </div>
                            <div class="form-group col-3 mb-0">
                                <label>
                                    <span class="mr-1 perc-click-span-editForm-second">
                                        <?= ($link['is_click'] ?? false) ? 'Clicks' : 'Percent (%)' ?>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Destinations Container -->
                        <div class="urls-container">

                            <!-- OS-Specific URLs -->
                            <div class="os-specific-urls">
                                <?php if ($link['os_filter_enabled'] ?? false) : ?>
                                    <?php foreach ($link['full'] as $index => $dest) : ?>
                                        <div class="form-row os-url-pair">
                                            <div class="form-group col-2 mb-2">
                                                <input class="form-control destination-num-os" type="text" disabled value="<?= $index + 1 ?>">
                                            </div>
                                            <div class="form-group col-7 mb-2 position-relative">
                                                <input class="form-control os-destination-input" type="url"
                                                    name="os_url[]" value="<?= htmlspecialchars($dest['url'] ?? 'https://') ?>"
                                                    placeholder="OS-specific destination..." required>
                                            </div>
                                            <div class="form-group col-3 mb-2">
                                                <select class="form-control os-select" name="os[]" required>
                                                    <?php foreach (['windows', 'macos', 'linux', 'ios', 'android', 'other'] as $os) : ?>
                                                        <option value="<?= $os ?>" <?= ($dest['os'] ?? '') === $os ? 'selected' : '' ?>>
                                                            <?= ucfirst($os) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="button" class="btn btn-danger delete-btn position-absolute">
                                                    <ion-icon name="close-outline"></ion-icon>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Regular URLs -->
                            <div class="regular-urls">
                                <?php if (!$link['os_filter_enabled'] ?? true) : ?>
                                    <?php foreach ($link['full'] as $index => $dest) : ?>
                                        <div class="form-row url-pair">
                                            <div class="form-group col-2 mb-2">
                                                <input class="form-control destination-num" type="text" disabled value="<?= $index + 1 ?>">
                                            </div>
                                            <div class="form-group col-7 mb-2">
                                                <input class="form-control destination-input" type="url"
                                                    name="url[]" value="<?= htmlspecialchars($dest['url'] ?? 'https://') ?>"
                                                    placeholder="destination url..." required>
                                            </div>
                                            <div class="form-group col-3 mb-2 position-relative">
                                                <input class="form-control percent-input" type="number"
                                                    name="<?= ($link['is_click'] ?? false) ? 'clicks[]' : 'perc[]' ?>"
                                                    value="<?= ($link['is_click'] ?? false) ? ($dest['clicks'] ?? '') : ($dest['perc'] ?? '') ?>"
                                                    placeholder="<?= ($link['is_click'] ?? false) ? 'Clicks' : '%' ?>"
                                                    min="0" <?= ($link['is_click'] ?? false) ? '' : 'max="100"' ?> required>
                                                <button type="button" class="btn btn-danger delete-btn position-absolute">
                                                    <ion-icon name="close-outline"></ion-icon>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                        </div>

                        <!-- Control Buttons -->
                        <div class="overflow-hidden py-1">
                            <div class="form-group float-left mb-0">
                                <button type="button" class="btn btn-secondary d-inline-flex align-items-center addFieldBtn">
                                    <ion-icon name="add-outline" class="mr-1"></ion-icon>Add Field
                                </button>
                            </div>

                            <!-- Distribution Toggles -->
                            <div class="d-flex" style="flex-direction: column;align-items: flex-end;gap: 8px;">
                                <?php if (!($link['os_filter_enabled'] ?? false)) : ?>
                                    <div class="form-group float-right mb-0 auto-man-button-div">
                                        <p class="mr-2">
                                            <span class="mr-2 auto-man-span-editForm"><?= ($link['is_equal_distribution'] ?? false) ? 'Auto' : 'Manual' ?></span>
                                            <i class="form-tooltip mr-2" title="Manual/Auto Percentage Allocation. Default: Auto">?</i>
                                        </p>
                                        <input id="auto-man-edit-input" type="checkbox"
                                            <?= ($link['is_equal_distribution'] ?? false) ? 'checked' : '' ?>>
                                        <label for="auto-man-edit-input" class="check-trail">
                                            <span class="check-handler"></span>
                                        </label>
                                    </div>
                                    <div class="form-group float-right mb-0 equal-dist-button-div">
                                        <p class="mr-2">
                                            <span class="mr-2 equal-dist-span-editForm">Equal</span>
                                            <i class="form-tooltip mr-2" title="Enable equal distribution among all destinations (overrides percentages)">?</i>
                                        </p>
                                        <input id="equal-dist-edit-input" type="checkbox" name="is_equal_distribution"
                                            <?= ($link['is_equal_distribution'] ?? false) ? 'checked' : '' ?>>
                                        <label for="equal-dist-edit-input" class="check-trail">
                                            <span class="check-handler"></span>
                                        </label>
                                    </div>
                                <?php endif; ?>
                                <div class="form-group float-right mb-0 os-filter-button-div">
                                    <p class="mr-2">
                                        <span class="mr-2 os-filter-span-editForm">OS Filter</span>
                                        <i class="form-tooltip mr-2" title="Enable OS-specific URL routing">?</i>
                                    </p>
                                    <input id="os-filter-editForm-input" type="checkbox" name="os_filter_enabled"
                                        <?= ($link['os_filter_enabled'] ?? false) ? 'checked' : '' ?>>
                                    <label for="os-filter-editForm-input" class="check-trail">
                                        <span class="check-handler"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr style="height: 1px; background-color: var(--background-tertiary);">
                        <p class="error-message float-left"></p>

                        <button type="submit" class="btn btn-primary float-right py-1">Update</button>
                        <button type="button" class="btn btn-secondary float-right py-1 mr-2 text-white reset-btn">Reset</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>


<?php include __DIR__ . '/../partials/footer.php'; ?>