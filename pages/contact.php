<!-- ══ CONTACT / ORDER PAGE ══ -->
<?php
$livestock_options = [
    '🐷 Lechon-Ready Pig',
    '🐽 Feeder Pig (Piglet)',
    '🐐 Native Goat (Kambing)',
    '🐑 Breeding Goat (Doe)',
    '🐔 Native Chicken (Manok)',
    '🥚 Farm Eggs (Itlog)',
];

$payment_options = [
    'Cash on Delivery',
    'GCash',
    'Bank Transfer',
    'Cash on Pickup',
];
?>

<div class="page-hero">
    <div class="container">
        <div class="section-label">Get In Touch</div>
        <h1>Place an <span style="color:var(--amber)">Order</span></h1>
        <p>Interested in our livestock? Fill in the form and we'll get back to you promptly.</p>
    </div>
</div>

<div class="page-content">
    <div class="container">
        <div class="row g-4">

            <!-- Order Form -->
            <div class="col-lg-7">
                <div class="contact-form-wrap">
                    <h5 style="font-weight:700;color:var(--green-deep);margin-bottom:1.5rem;font-size:1.05rem">
                        Customer Order Form
                    </h5>

                    <form id="orderForm">
                        <div class="row g-3">

                            <div class="col-sm-6">
                                <label class="form-label-sm">Full Name</label>
                                <input type="text" name="full_name" class="form-control-custom"
                                       placeholder="Juan dela Cruz" required />
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label-sm">Contact Number</label>
                                <input type="tel" name="contact" class="form-control-custom"
                                       placeholder="09XX XXX XXXX" required />
                            </div>

                            <div class="col-12">
                                <label class="form-label-sm">Email Address</label>
                                <input type="email" name="email" class="form-control-custom"
                                       placeholder="juan@email.com" />
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label-sm">Livestock Type</label>
                                <select name="livestock" class="form-control-custom" required>
                                    <option value="">Select livestock...</option>
                                    <?php foreach ($livestock_options as $opt): ?>
                                        <option value="<?= $opt ?>"><?= $opt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label-sm">Quantity / Weight</label>
                                <input type="text" name="quantity" class="form-control-custom"
                                       placeholder="e.g. 2 heads / 5 kg" required />
                            </div>

                            <div class="col-12">
                                <label class="form-label-sm">Delivery Address</label>
                                <input type="text" name="address" class="form-control-custom"
                                       placeholder="Barangay, Municipality, Batangas" required />
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label-sm">Preferred Delivery Date</label>
                                <input type="date" name="delivery_date" class="form-control-custom" />
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label-sm">Payment Method</label>
                                <select name="payment" class="form-control-custom">
                                    <?php foreach ($payment_options as $pay): ?>
                                        <option value="<?= $pay ?>"><?= $pay ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label-sm">Additional Notes</label>
                                <textarea name="notes" class="form-control-custom" rows="3"
                                          placeholder="Special requests, slaughter preference, etc."></textarea>
                            </div>

                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="bi bi-send"></i> Submit Order Request
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="contact-info-card">
                    <h4>Contact Information</h4>
                    <p>We're available to assist you with inquiries, orders, and delivery arrangements.</p>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <h6>Farm Address</h6>
                            <p>St. Vincent Farm, Batangas, Calabarzon, Philippines</p>
                        </div>
                    </div>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <h6>Phone / Viber</h6>
                            <p>+63 9XX XXX XXXX</p>
                        </div>
                    </div>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-envelope-fill"></i></div>
                        <div>
                            <h6>Email</h6>
                            <p>orders@stvincentfarm.ph</p>
                        </div>
                    </div>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-clock-fill"></i></div>
                        <div>
                            <h6>Operating Hours</h6>
                            <p>Monday – Saturday<br>6:00 AM – 5:00 PM</p>
                        </div>
                    </div>

                    <div class="contact-note">
                        <p>Delivery available within Batangas area. Advance orders recommended especially for events.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
