<?php // pages/profile.php ?>
<?php

// SAFE DEFAULTS
$name  = $user['name'] ?? 'User';
$email = $user['email'] ?? '-';
$role  = $user['role'] ?? '-';
$phone = $user['contact_number'] ?? '-';

$address = $user['address'] ?? '';
$firstName = $user['first_name'] ?? '';
$lastName  = $user['last_name'] ?? '';

$dateJoined = isset($user['date_created'])
    ? date('F Y', strtotime($user['date_created']))
    : '-';

?>
<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <h5 class="page-heading mb-1">My Profile</h5>
        <p class="text-muted mb-0 small">Manage your account details</p>
    </div>
</div>

<div class="row g-4">

    <!-- Profile Card -->
    <div class="col-12 col-lg-4">
        <div class="card-panel text-center profile-card">
            <div class="profile-avatar-wrap">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($name) ?>&background=0B6B1C&color=fff&size=160"
                     alt="Avatar" class="profile-avatar" />
                <button class="avatar-change-btn" title="Change photo">
                    <i class="bi bi-camera"></i>
                </button>
            </div>
            <h6 class="profile-name mt-3 mb-1"><?= htmlspecialchars($name) ?></h6>
            <span class="profile-role-tag"><?= htmlspecialchars($role) ?></span>
            <hr class="my-3" />
            <ul class="profile-meta-list">
                <li><i class="bi bi-envelope"></i> <?= htmlspecialchars($email) ?></li>
                <li><i class="bi bi-telephone"></i> <?= htmlspecialchars($phone) ?></li>
                <li><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($address) ?></li>
                <li><i class="bi bi-calendar-check"></i> Joined <?= $dateJoined ?></li>
            </ul>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="col-12 col-lg-8">
        <div class="card-panel">
            <div class="card-panel-header mb-3">
                <span>Edit Information</span>
            </div>

            <!-- Tab Nav -->
            <ul class="nav nav-tabs profile-tabs mb-4" id="profileTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">General</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button">Security</button>
                </li>
            </ul>

            <div class="tab-content" id="profileTabContent">

                <!-- General Tab -->
                <form id="profileForm">
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($firstName) ?>" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($lastName) ?>" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($phone) ?>" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($role) ?>" readonly />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($address) ?>" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Bio</label>
                            <textarea class="form-control" rows="3">System user of the platform.</textarea>
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-green">
                                <i class="bi bi-check2 me-1"></i> Save Changes
                            </button>
                        </div>
                      
                        
                    </div>
                </div>
                </form>
                
                <!-- Security Tab -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" placeholder="Enter current password" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" placeholder="New password" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" placeholder="Confirm new password" />
                        </div>
                        <div class="col-12">
                            <div class="password-rules">
                                <p class="mb-1 small fw-semibold">Password must contain:</p>
                                <ul class="small text-muted ps-3 mb-0">
                                    <li>At least 8 characters</li>
                                    <li>One uppercase letter</li>
                                    <li>One number or special character</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-green">
                                <i class="bi bi-shield-lock me-1"></i> Update Password
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
