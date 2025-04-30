<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="content-wrapper vertically-center">
    <div class="container content">
        <div class="row">
            <div class="col-lg-12">
                <div class="content-container form-content-settings shadow-sm rounded">
                    <h3 class="pt-3 pl-4 pb-3">Change Password</h3>
                                
                    <?php if (!empty($errors)): ?>
                        <div class=" mb-4">
                            <?php foreach ($errors as $error): ?>
                                <p class="mb-1"><?= htmlspecialchars($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($success ?? false): ?>
                        <div class="alert alert-success mb-4">
                            Password changed successfully!
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="clearfix create-edit-form">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" class="form-control" 
                                   name="current_password" 
                                   autocomplete="current-password" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-control" 
                                   name="new_password" 
                                   autocomplete="new-password" 
                                   minlength="8"
                                   required>
                            <small class="form-text text-muted">
                                Minimum 8 characters
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" class="form-control" 
                                   name="confirm_password" 
                                   autocomplete="new-password" 
                                   required>
                        </div>

                        <hr class="my-3" style="height: 1px; background-color: var(--background-tertiary);">
                        
                        <button type="submit" class="btn btn-primary float-right py-1">
                            Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>