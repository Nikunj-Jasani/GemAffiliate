<div class="affiliate-portal-container">
    <div class="affiliate-auth-grid">
        <!-- Left Column - Company Branding -->
        <div class="affiliate-brand-column">
            <div class="affiliate-brand-content">
                <div class="affiliate-logo-container">
                    <?php 
                    $custom_logo = get_option('affiliate_portal_logo', '');
                    if ($custom_logo): ?>
                        <img src="<?php echo esc_url($custom_logo); ?>" alt="Company Logo" class="affiliate-custom-logo" style="max-width: 80px; height: auto;">
                    <?php else: ?>
                        <svg class="affiliate-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="80" height="80">
                            <defs>
                                <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#0d6efd;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#6610f2;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <circle cx="50" cy="50" r="45" fill="url(#logoGradient)" stroke="white" stroke-width="2"/>
                            <path d="M30 75 L50 25 L70 75 M37 60 L63 60" 
                                  stroke="white" 
                                  stroke-width="4" 
                                  stroke-linecap="round" 
                                  stroke-linejoin="round" 
                                  fill="none"/>
                            <circle cx="25" cy="25" r="2" fill="white" opacity="0.8"/>
                            <circle cx="75" cy="25" r="2" fill="white" opacity="0.8"/>
                            <circle cx="85" cy="65" r="1.5" fill="white" opacity="0.6"/>
                            <line x1="15" y1="40" x2="25" y2="35" stroke="white" stroke-width="1.5" opacity="0.7"/>
                            <line x1="75" y1="35" x2="85" y2="40" stroke="white" stroke-width="1.5" opacity="0.7"/>
                            <line x1="20" y1="70" x2="30" y2="65" stroke="white" stroke-width="1.5" opacity="0.7"/>
                        </svg>
                    <?php endif; ?>
                </div>
                <?php
                // Get custom brand settings
                $brand_title = get_option('affiliate_portal_brand_title', 'Join Our Network');
                $brand_slogan = get_option('affiliate_portal_brand_slogan', 'Start your affiliate journey with us');
                ?>
                <h1 class="affiliate-brand-title"><?php echo esc_html($brand_title); ?></h1>
                <p class="affiliate-brand-slogan"><?php echo esc_html($brand_slogan); ?></p>
                <div class="affiliate-brand-features">
                    <div class="affiliate-feature-item">
                        <svg class="affiliate-feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2.81 14.12L5.64 11.29C8.34 10.95 10.7 9.17 12.2 6.69C13.7 4.22 14.24 1.33 13.81 0.1L13.79 0C13.78 0 13.78 0 13.77 0C13.3 0 12.9 0.25 12.69 0.64L10.06 5.86C9.5 7 8.45 7.88 7.22 8.32L2.63 9.82C2.09 9.97 1.74 10.46 1.74 11C1.74 11.54 2.09 12.03 2.63 12.18L2.81 14.12Z"/>
                        </svg>
                        <span>Quick Setup</span>
                    </div>
                    <div class="affiliate-feature-item">
                        <svg class="affiliate-feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                        </svg>
                        <span>Competitive Rates</span>
                    </div>
                    <div class="affiliate-feature-item">
                        <svg class="affiliate-feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.9,17.39C17.64,16.59 16.89,16 16,16H15V13A1,1 0 0,0 14,12H8V10H10A1,1 0 0,0 11,9V7H13A2,2 0 0,0 15,5V4.59C17.93,5.77 20,8.64 20,12C20,14.08 19.2,15.97 17.9,17.39M11,19.93C7.05,19.44 4,16.08 4,12C4,11.38 4.08,10.78 4.21,10.21L9,15V16A2,2 0 0,0 11,18M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                        </svg>
                        <span>Global Reach</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Registration Form -->
        <div class="affiliate-form-column">
            <div class="affiliate-form-container">
                <div class="affiliate-form-header">
                    <h2>Create Account</h2>
                    <p>Fill in your information to get started</p>
                </div>
                
                <!-- Step Indicator -->
                <div class="affiliate-step-indicator">
                    <div class="affiliate-step active" data-step="1">
                        <div class="affiliate-step-number">1</div>
                        <div class="affiliate-step-label">Credentials</div>
                    </div>
                    <div class="affiliate-step" data-step="2">
                        <div class="affiliate-step-number">2</div>
                        <div class="affiliate-step-label">Personal</div>
                    </div>
                    <div class="affiliate-step" data-step="3">
                        <div class="affiliate-step-number">3</div>
                        <div class="affiliate-step-label">Contact</div>
                    </div>
                    <div class="affiliate-step" data-step="4">
                        <div class="affiliate-step-number">4</div>
                        <div class="affiliate-step-label">Address</div>
                    </div>
                    <div class="affiliate-step" data-step="5">
                        <div class="affiliate-step-number">5</div>
                        <div class="affiliate-step-label">Business</div>
                    </div>
                </div>

                <form class="affiliate-form affiliate-multi-step-form" id="affiliateRegistrationForm">
                    <!-- Step 1: Basic Credentials -->
                    <div class="affiliate-form-step active" data-step="1">
                        <h3 class="affiliate-step-title">Basic Credentials</h3>
                        <div class="affiliate-form-group">
                            <label for="affiliate_reg_username" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                                </svg>
                                Username *
                            </label>
                            <input type="text" id="affiliate_reg_username" name="username" class="affiliate-form-control" required>
                        </div>
                        
                        <div class="affiliate-form-group">
                            <label for="affiliate_reg_password" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                                </svg>
                                Password *
                            </label>
                            <input type="password" id="affiliate_reg_password" name="password" class="affiliate-form-control" required>
                        </div>
                        
                        <div class="affiliate-form-group">
                            <label for="affiliate_reg_password_confirm" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                                </svg>
                                Confirm Password *
                            </label>
                            <input type="password" id="affiliate_reg_password_confirm" name="password_confirm" class="affiliate-form-control" required>
                        </div>
                        
                        <div class="affiliate-form-group">
                            <label for="security_que" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M15.07,11.25L14.17,12.17C13.45,12.89 13,13.5 13,15H11V14.5C11,13.39 11.45,12.39 12.17,11.67L13.41,10.41C13.78,10.05 14,9.55 14,9C14,7.89 13.1,7 12,7A2,2 0 0,0 10,9H8A4,4 0 0,1 12,5A4,4 0 0,1 16,9C16,10.25 15.07,11.25 15.07,11.25M13,19H11V17H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12C22,6.47 17.5,2 12,2Z"/>
                                </svg>
                                Security Question *
                            </label>
                            <select id="security_que" name="security_que" class="affiliate-form-control" required>
                                <option value="">Select a security question</option>
                                <option value="Anniversary (MM/DD/YYYY)?">Anniversary (MM/DD/YYYY)?</option>
                                <option value="Father's Middle Name?">Father's Middle Name?</option>
                                <option value="Favorite Sports Team?">Favorite Sports Team?</option>
                                <option value="Favorite Teacher's Name?">Favorite Teacher's Name?</option>
                                <option value="First Child's Middle Name?">First Child's Middle Name?</option>
                                <option value="High School Name?">High School Name?</option>
                                <option value="Spouse's Middle Name?">Spouse's Middle Name?</option>
                                <option value="Your Favorite Movie?">Your Favorite Movie?</option>
                                <option value="Your Favorite Pet's Name?">Your Favorite Pet's Name?</option>
                            </select>
                        </div>
                        
                        <div class="affiliate-form-group">
                            <label for="security_ans" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7,14A2,2 0 0,1 5,12A2,2 0 0,1 7,10A2,2 0 0,1 9,12A2,2 0 0,1 7,14M12.65,10C11.83,7.67 9.61,6 7,6A6,6 0 0,0 1,12A6,6 0 0,0 7,18C9.61,18 11.83,16.33 12.65,14H17V18H21V14H23V10H12.65Z"/>
                                </svg>
                                Security Answer *
                            </label>
                            <input type="text" id="security_ans" name="security_ans" class="affiliate-form-control" required>
                        </div>
                    </div>
                    
                    <!-- Step 2: Personal Information -->
                    <div class="affiliate-form-step" data-step="2">
                        <h3 class="affiliate-step-title">Personal Information</h3>
                        <div class="affiliate-form-group">
                            <label class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,17C12.5,17 13,16.5 13,16V12C13,11.5 12.5,11 12,11C11.5,11 11,11.5 11,12V16C11,16.5 11.5,17 12,17M12,9A1,1 0 0,0 13,8A1,1 0 0,0 12,7A1,1 0 0,0 11,8A1,1 0 0,0 12,9Z"/>
                                </svg>
                                Prefix *
                            </label>
                            <div class="affiliate-prefix-selector">
                                <input type="radio" id="prefix_mr" name="name_prefix" value="Mr" class="affiliate-prefix-radio" required>
                                <label for="prefix_mr" class="affiliate-prefix-btn">Mr</label>
                                
                                <input type="radio" id="prefix_ms" name="name_prefix" value="Ms" class="affiliate-prefix-radio" required>
                                <label for="prefix_ms" class="affiliate-prefix-btn">Ms</label>
                                
                                <input type="radio" id="prefix_mrs" name="name_prefix" value="Mrs" class="affiliate-prefix-radio" required>
                                <label for="prefix_mrs" class="affiliate-prefix-btn">Mrs</label>
                                
                                <input type="radio" id="prefix_miss" name="name_prefix" value="Miss" class="affiliate-prefix-radio" required>
                                <label for="prefix_miss" class="affiliate-prefix-btn">Miss</label>
                            </div>
                        </div>
                        
                        <div class="affiliate-form-row">
                            <div class="affiliate-form-group affiliate-col-md-4">
                                <label for="first_name" class="affiliate-form-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                                    </svg>
                                    First Name *
                                </label>
                                <input type="text" id="first_name" name="first_name" class="affiliate-form-control" required>
                            </div>
                            <div class="affiliate-form-group affiliate-col-md-5">
                                <label for="last_name" class="affiliate-form-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                                    </svg>
                                    Last Name *
                                </label>
                                <input type="text" id="last_name" name="last_name" class="affiliate-form-control" required>
                            </div>
                        </div>
                        
                        <div class="affiliate-form-group">
                            <label for="dob" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9,10V12H7V10H9M13,10V12H11V10H13M17,10V12H15V10H17M19,3A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5C3.89,21 3,20.1 3,19V5A2,2 0 0,1 5,3H6V1H8V3H16V1H18V3H19M19,19V8H5V19H19M9,14V16H7V14H9M13,14V16H11V14H13M17,14V16H15V14H17Z"/>
                                </svg>
                                Date of Birth *
                            </label>
                            <input type="date" id="dob" name="dob" class="affiliate-form-control" required>
                        </div>
                        
                        <div class="affiliate-form-group">
                            <label class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M10,2H14A2,2 0 0,1 16,4V6H20A2,2 0 0,1 22,8V19A2,2 0 0,1 20,21H4A2,2 0 0,1 2,19V8A2,2 0 0,1 4,6H8V4A2,2 0 0,1 10,2M14,6V4H10V6H14Z"/>
                                </svg>
                                Account Type *
                            </label>
                            <div class="affiliate-account-type-selector">
                                <input type="radio" id="type_individual" name="account_type" value="Individual" class="affiliate-type-radio" required>
                                <label for="type_individual" class="affiliate-type-btn">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                                    </svg>
                                    Individual
                                </label>
                                
                                <input type="radio" id="type_company" name="account_type" value="Company" class="affiliate-type-radio" required>
                                <label for="type_company" class="affiliate-type-btn">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12,7V3H2V21H22V7H12M6,19H4V17H6V19M6,15H4V13H6V15M6,11H4V9H6V11M6,7H4V5H6V7M10,19H8V17H10V19M10,15H8V13H10V15M10,11H8V9H10V11M10,7H8V5H10V7M20,19H12V17H14V15H12V13H14V11H12V9H20V19M18,11H16V13H18V11M18,15H16V17H18V15Z"/>
                                    </svg>
                                    Company
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 3: Contact Details -->
                    <div class="affiliate-form-step" data-step="3">
                        <h3 class="affiliate-step-title">Contact Details</h3>
                        <div class="affiliate-form-group">
                            <label for="reg_email" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                                </svg>
                                Email Address *
                            </label>
                            <input type="email" id="reg_email" name="email" class="affiliate-form-control" required>
                        </div>
                        
                        <div class="affiliate-form-group">
                            <label for="company_name" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,7V3H2V21H22V7H12M6,19H4V17H6V19M6,15H4V13H6V15M6,11H4V9H6V11M6,7H4V5H6V7M10,19H8V17H10V19M10,15H8V13H10V15M10,11H8V9H10V11M10,7H8V5H10V7M20,19H12V17H14V15H12V13H14V11H12V9H20V19M18,11H16V13H18V11M18,15H16V17H18V15Z"/>
                                </svg>
                                Company Name
                            </label>
                            <input type="text" id="company_name" name="company_name" class="affiliate-form-control">
                        </div>
                        
                        <div class="affiliate-form-row">
                            <div class="affiliate-form-group affiliate-col-md-4">
                                <label for="country_code" class="affiliate-form-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M6,3A1,1 0 0,1 7,4V4.88C8.06,4.44 9.5,4 11,4C14,4 14,6 16,6C19,6 20,4 20,4V12C20,12 19,14 16,14C14,14 14,12 11,12C9.5,12 8.06,12.44 7,12.88V20A1,1 0 0,1 6,21A1,1 0 0,1 5,20V4A1,1 0 0,1 6,3Z"/>
                                    </svg>
                                    Country Code *
                                </label>
                                <select id="country_code" name="country_code" class="affiliate-form-control affiliate-country-select affiliate-modern-select" required>
                                    <option value="">Select Country Code</option>
                                    <option value="+54">Argentina (+54)</option>
                                    <option value="+61">Australia (+61)</option>
                                    <option value="+43">Austria (+43)</option>
                                    <option value="+32">Belgium (+32)</option>
                                    <option value="+55">Brazil (+55)</option>
                                    <option value="+1">Canada (+1)</option>
                                    <option value="+56">Chile (+56)</option>
                                    <option value="+86">China (+86)</option>
                                    <option value="+420">Czech Republic (+420)</option>
                                    <option value="+45">Denmark (+45)</option>
                                    <option value="+20">Egypt (+20)</option>
                                    <option value="+358">Finland (+358)</option>
                                    <option value="+33">France (+33)</option>
                                    <option value="+49">Germany (+49)</option>
                                    <option value="+30">Greece (+30)</option>
                                    <option value="+91">India (+91)</option>
                                    <option value="+62">Indonesia (+62)</option>
                                    <option value="+353">Ireland (+353)</option>
                                    <option value="+972">Israel (+972)</option>
                                    <option value="+39">Italy (+39)</option>
                                    <option value="+81">Japan (+81)</option>
                                    <option value="+60">Malaysia (+60)</option>
                                    <option value="+52">Mexico (+52)</option>
                                    <option value="+31">Netherlands (+31)</option>
                                    <option value="+64">New Zealand (+64)</option>
                                    <option value="+47">Norway (+47)</option>
                                    <option value="+63">Philippines (+63)</option>
                                    <option value="+48">Poland (+48)</option>
                                    <option value="+351">Portugal (+351)</option>
                                    <option value="+966">Saudi Arabia (+966)</option>
                                    <option value="+65">Singapore (+65)</option>
                                    <option value="+27">South Africa (+27)</option>
                                    <option value="+82">South Korea (+82)</option>
                                    <option value="+34">Spain (+34)</option>
                                    <option value="+46">Sweden (+46)</option>
                                    <option value="+41">Switzerland (+41)</option>
                                    <option value="+66">Thailand (+66)</option>
                                    <option value="+90">Turkey (+90)</option>
                                    <option value="+971">United Arab Emirates (+971)</option>
                                    <option value="+44">United Kingdom (+44)</option>
                                    <option value="+1">United States (+1)</option>
                                    <option value="+84">Vietnam (+84)</option>
                                </select>
                            </div>
                            <div class="affiliate-form-group affiliate-col-md-8">
                                <label for="mobile_number" class="affiliate-form-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/>
                                    </svg>
                                    Mobile Number *
                                </label>
                                <input type="tel" id="mobile_number" name="mobile_number" class="affiliate-form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 4: Address Information -->
                    <div class="affiliate-form-step" data-step="4">
                        <h3 class="affiliate-step-title">Address Information</h3>
                        <div class="affiliate-form-group">
                            <label for="address_line1" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                </svg>
                                Address Line 1 *
                            </label>
                            <input type="text" id="address_line1" name="address_line1" class="affiliate-form-control" required>
                        </div>
                        
                        <div class="affiliate-form-group">
                            <label for="address_line2" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                </svg>
                                Address Line 2
                            </label>
                            <input type="text" id="address_line2" name="address_line2" class="affiliate-form-control">
                        </div>
                        
                        <div class="affiliate-form-row">
                            <div class="affiliate-form-group affiliate-col-md-6">
                                <label for="city" class="affiliate-form-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19,15H17V13H19M19,19H17V17H19M13,7H11V5H13M13,11H11V9H13M13,15H11V13H13M13,19H11V17H13M7,11H5V9H7M7,15H5V13H7M7,19H5V17H7M15,11V5L12,2L9,5V7H3V21H21V11H15Z"/>
                                    </svg>
                                    City *
                                </label>
                                <input type="text" id="city" name="city" class="affiliate-form-control" required>
                            </div>
                            <div class="affiliate-form-group affiliate-col-md-6">
                                <label for="zipcode" class="affiliate-form-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                                    </svg>
                                    ZIP Code *
                                </label>
                                <input type="text" id="zipcode" name="zipcode" class="affiliate-form-control" required>
                            </div>
                        </div>
                        
                        <div class="affiliate-form-row">
                            <div class="affiliate-form-group affiliate-col-md-6">
                                <label for="country" class="affiliate-form-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.9,17.39C17.64,16.59 16.89,16 16,16H15V13A1,1 0 0,0 14,12H8V10H10A1,1 0 0,0 11,9V7H13A2,2 0 0,0 15,5V4.59C17.93,5.77 20,8.64 20,12C20,14.08 19.2,15.97 17.9,17.39M11,19.93C7.05,19.44 4,16.08 4,12C4,11.38 4.08,10.78 4.21,10.21L9,15V16A2,2 0 0,0 11,18M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                                    </svg>
                                    Country *
                                </label>
                                <select id="country" name="country" class="affiliate-form-control affiliate-country-select affiliate-modern-select" required>
                                    <option value="">Select Country</option>
                                    <option value="Argentina" data-country-id="35">Argentina</option>
                                    <option value="Australia" data-country-id="4">Australia</option>
                                    <option value="Austria" data-country-id="12">Austria</option>
                                    <option value="Belgium" data-country-id="10">Belgium</option>
                                    <option value="Brazil" data-country-id="34">Brazil</option>
                                    <option value="Canada" data-country-id="3">Canada</option>
                                    <option value="Chile" data-country-id="36">Chile</option>
                                    <option value="China" data-country-id="24">China</option>
                                    <option value="Czech Republic" data-country-id="18">Czech Republic</option>
                                    <option value="Denmark" data-country-id="15">Denmark</option>
                                    <option value="Egypt" data-country-id="42">Egypt</option>
                                    <option value="Finland" data-country-id="16">Finland</option>
                                    <option value="France" data-country-id="6">France</option>
                                    <option value="Germany" data-country-id="5">Germany</option>
                                    <option value="Greece" data-country-id="20">Greece</option>
                                    <option value="India" data-country-id="25">India</option>
                                    <option value="Indonesia" data-country-id="30">Indonesia</option>
                                    <option value="Ireland" data-country-id="21">Ireland</option>
                                    <option value="Israel" data-country-id="39">Israel</option>
                                    <option value="Italy" data-country-id="7">Italy</option>
                                    <option value="Japan" data-country-id="22">Japan</option>
                                    <option value="Malaysia" data-country-id="27">Malaysia</option>
                                    <option value="Mexico" data-country-id="37">Mexico</option>
                                    <option value="Netherlands" data-country-id="9">Netherlands</option>
                                    <option value="New Zealand" data-country-id="32">New Zealand</option>
                                    <option value="Norway" data-country-id="14">Norway</option>
                                    <option value="Philippines" data-country-id="29">Philippines</option>
                                    <option value="Poland" data-country-id="17">Poland</option>
                                    <option value="Portugal" data-country-id="19">Portugal</option>
                                    <option value="Saudi Arabia" data-country-id="41">Saudi Arabia</option>
                                    <option value="Singapore" data-country-id="26">Singapore</option>
                                    <option value="South Africa" data-country-id="33">South Africa</option>
                                    <option value="South Korea" data-country-id="23">South Korea</option>
                                    <option value="Spain" data-country-id="8">Spain</option>
                                    <option value="Sweden" data-country-id="13">Sweden</option>
                                    <option value="Switzerland" data-country-id="11">Switzerland</option>
                                    <option value="Thailand" data-country-id="28">Thailand</option>
                                    <option value="Turkey" data-country-id="38">Turkey</option>
                                    <option value="United Arab Emirates" data-country-id="40">United Arab Emirates</option>
                                    <option value="United Kingdom" data-country-id="2">United Kingdom</option>
                                    <option value="United States" data-country-id="1">United States</option>
                                    <option value="Vietnam" data-country-id="31">Vietnam</option>
                                </select>
                            </div>
                            <div class="affiliate-form-group affiliate-col-md-6">
                                <label for="state" class="affiliate-form-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20.5,3L20.34,3.03L15,5.1L9,3L3.36,4.9C3.15,4.97 3,5.15 3,5.38V20.5A0.5,0.5 0 0,0 3.5,21L3.66,20.97L9,18.9L15,21L20.64,19.1C20.85,19.03 21,18.85 21,18.62V3.5A0.5,0.5 0 0,0 20.5,3Z"/>
                                    </svg>
                                    State/Province *
                                </label>
                                <select id="state" name="state" class="affiliate-form-control" required disabled>
                                    <option value="">Select Country First</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 5: Business Details -->
                    <div class="affiliate-form-step" data-step="5">
                        <h3 class="affiliate-step-title">Business Details</h3>
                        <div class="affiliate-form-group">
                            <label for="chat_id_channel" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9Z"/>
                                </svg>
                                Telegram/Teams *
                            </label>
                            <input type="text" id="chat_id_channel" name="chat_id_channel" class="affiliate-form-control" required>
                        </div>
                        
                        <div class="affiliate-form-group">
                            <label for="affiliate_type" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M5.5,9A1.5,1.5 0 0,0 7,7.5A1.5,1.5 0 0,0 5.5,6A1.5,1.5 0 0,0 4,7.5A1.5,1.5 0 0,0 5.5,9M17.41,11.58C17.77,11.94 18,12.44 18,13C18,13.55 17.78,14.05 17.41,14.41L12.41,19.41C12.05,19.77 11.55,20 11,20C10.45,20 9.95,19.78 9.59,19.41L2.59,12.41C2.22,12.05 2,11.55 2,11V4C2,2.89 2.89,2 4,2H11C11.55,2 12.05,2.22 12.41,2.59L19.41,9.59C19.77,9.95 20,10.45 20,11C20,11.55 19.78,12.05 19.41,12.41L17.41,11.58Z"/>
                                </svg>
                                Affiliate Type *
                            </label>
                            <select id="affiliate_type" name="affiliate_type" class="affiliate-form-control" required>
                                <option value="">Please Select</option>
                                <option value="PPC Affiliates">PPC Affiliates</option>
                                <option value="SEO Affiliates">SEO Affiliates</option>
                                <option value="Mobile Affiliates">Mobile Affiliates</option>
                                <option value="Comparison Sites">Comparison Sites</option>
                                <option value="Social media Publishers">Social media Publishers</option>
                                <option value="Content Affiliates">Content Affiliates</option>
                                <option value="Sub-Affiliate Networks">Sub-Affiliate Networks</option>
                                <option value="Loyalty and cashback sites">Loyalty and cashback sites</option>
                                <option value="Coupon/Bonus Affiliates">Coupon/Bonus Affiliates</option>
                                <option value="News and media sites">News and media sites</option>
                                <option value="Review sites">Review sites</option>
                                <option value="Email Marketing">Email Marketing</option>
                                <option value="Incentivized Traffic Affiliates">Incentivized Traffic Affiliates</option>
                                <option value="Ranking and reviewing websites">Ranking and reviewing websites</option>
                                <option value="Non-profit-oriented affiliates or Charities">Non-profit-oriented affiliates or Charities</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        
                        <div class="affiliate-form-group">
                            <label for="currency" class="affiliate-form-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                                </svg>
                                Preferred Currency *
                            </label>
                            <select id="currency" name="currency" class="affiliate-form-control" required>
                                <option value="">Select currency</option>
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="BRL">BRL - Brazilian Real</option>
                            </select>
                        </div>
                        
                        <!-- Terms and Conditions Checkbox -->
                        <div class="affiliate-form-group">
                            <div class="affiliate-terms-checkbox">
                                <input type="checkbox" id="terms_conditions" name="terms_conditions" class="affiliate-checkbox" required>
                                <label for="terms_conditions" class="affiliate-terms-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9Z"/>
                                    </svg>
                                    I agree to the <a href="/termcondition/" target="_blank" class="affiliate-terms-link">Terms and Conditions</a> *
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Navigation -->
                    <div class="affiliate-form-navigation">
                        <button type="button" class="affiliate-btn affiliate-btn-secondary affiliate-btn-prev" style="display: none;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                            </svg>
                            Previous
                        </button>
                        <button type="button" class="affiliate-btn affiliate-btn-primary affiliate-btn-next">
                            Next
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"/>
                            </svg>
                        </button>
                        <button type="submit" class="affiliate-btn affiliate-btn-success affiliate-btn-submit" style="display: none;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12Z"/>
                            </svg>
                            Create Account
                        </button>
                    </div>
                </form>

                <div class="affiliate-auth-footer">
                    <div class="affiliate-auth-links">
                        <p>Already have an account? <a href="<?php echo home_url('/affiliate-login/'); ?>" class="affiliate-auth-link">Sign in here</a></p>
                    </div>
                    <div class="affiliate-home-redirect">
                        <a href="<?php echo home_url('/'); ?>" class="affiliate-btn affiliate-btn-outline affiliate-btn-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/>
                            </svg>
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load script with proper PHP variables
window.affiliate_ajax = {
    ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
    nonce: '<?php echo wp_create_nonce('affiliate_nonce'); ?>'
};
</script>
<script src="<?php echo plugin_dir_url(__FILE__) . '../assets/script.js'; ?>?v=<?php echo time(); ?>"></script>