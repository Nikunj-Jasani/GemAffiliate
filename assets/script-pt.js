document.addEventListener('DOMContentLoaded', function() {
    
    // Multi-step form functionality
    const form = document.getElementById('affiliateRegistrationForm');
    if (form) {
        initMultiStepForm();
    }
    
    // Login form functionality
    const loginForm = document.getElementById('affiliateLoginForm');
    if (loginForm) {
        initLoginForm();
    }
    
    // Form validation
    initFormValidation();
    
    // Load countries data
    loadCountriesData();
    
    // Add modern animations to form elements
    initModernAnimations();
    
    // Initialize admin functionality if on admin pages
    initAdminFunctionality();
});

function initLoginForm() {
    const loginForm = document.getElementById('affiliateLoginForm');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const username = document.getElementById('affiliate_username').value;
        const password = document.getElementById('affiliate_password').value;
        
        if (!username || !password) {
            showMessage('Por favor, preencha todos os campos', 'danger');
            
            // Highlight empty fields
            if (!username) {
                const usernameField = document.getElementById('affiliate_username');
                usernameField.classList.add('is-invalid');
                usernameField.focus();
            }
            if (!password) {
                const passwordField = document.getElementById('affiliate_password');
                passwordField.classList.add('is-invalid');
            }
            
            return;
        }
        
        // Clear any previous validation errors
        const usernameField = document.getElementById('affiliate_username');
        const passwordField = document.getElementById('affiliate_password');
        usernameField.classList.remove('is-invalid');
        passwordField.classList.remove('is-invalid');
        
        // Show loading state with proper styling
        const button = loginForm.querySelector('button[type="submit"]');
        button.classList.add('loading');
        button.disabled = true;
        const originalText = button.innerHTML;
        const originalStyles = {
            fontSize: button.style.fontSize,
            padding: button.style.padding,
            width: button.style.width,
            height: button.style.height
        };
        
        // Apply fixed styling during loading
        button.style.fontSize = '1rem';
        button.style.padding = '12px 24px';
        button.style.width = button.offsetWidth + 'px';
        button.style.height = '48px';
        button.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="3" fill="currentColor"/></svg> Entrando...';
        
        // Prepare data
        const formData = new FormData();
        formData.append('action', 'affiliate_login');
        formData.append('username', username);
        formData.append('password', password);
        formData.append('nonce', affiliate_ajax.nonce);
        
        // Submit via AJAX
        fetch(affiliate_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Reset button state
            button.classList.remove('loading');
            button.disabled = false;
            button.innerHTML = originalText;
            button.style.fontSize = originalStyles.fontSize;
            button.style.padding = originalStyles.padding;
            button.style.width = originalStyles.width;
            button.style.height = originalStyles.height;
            
            if (data.success) {
                showMessage(data.data.message, 'success');
                setTimeout(() => {
                    window.location.href = data.data.redirect;
                }, 1000);
            } else {
                showMessage(data.data || 'Falha no login', 'danger');
            }
        })
        .catch(error => {
            // Reset button state
            button.classList.remove('loading');
            button.disabled = false;
            button.innerHTML = originalText;
            button.style.fontSize = originalStyles.fontSize;
            button.style.padding = originalStyles.padding;
            button.style.width = originalStyles.width;
            button.style.height = originalStyles.height;
            
            showMessage('Ocorreu um erro. Tente novamente.', 'danger');
        });
    });
}

function initMultiStepForm() {
    const form = document.getElementById('affiliateRegistrationForm');
    const steps = document.querySelectorAll('.affiliate-form-step');
    const stepIndicators = document.querySelectorAll('.affiliate-step-indicator .affiliate-step');
    const nextBtn = document.querySelector('.affiliate-btn-next');
    const prevBtn = document.querySelector('.affiliate-btn-prev');
    const submitBtn = document.querySelector('.affiliate-btn-submit');
    
    let currentStep = 1;
    const totalSteps = steps.length;
    
    // Initialize form display
    updateForm();
    
    // Navigation event listeners
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateForm();
                }
            }
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                updateForm();
            }
        });
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateCurrentStep()) {
            return;
        }
        
        const hideLoading = showLoading(submitBtn);
        
        // Collect all form data
        const formData = new FormData(form);
        formData.append('action', 'affiliate_register');
        formData.append('nonce', affiliate_ajax.nonce);
        
        // Submit via AJAX
        fetch(affiliate_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showMessage(data.data.message, 'success');
                setTimeout(() => {
                    window.location.href = data.data.redirect;
                }, 2000);
            } else {
                showMessage(data.data || 'Falha no registro', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showMessage('Ocorreu um erro. Tente novamente.', 'danger');
        });
    });
    
    // Update form display based on current step
    function updateForm() {
        // Hide all steps first
        steps.forEach(step => {
            step.classList.remove('active');
            step.style.display = 'none';
        });
        
        // Show current step
        const currentStepElement = document.querySelector(`.affiliate-form-step[data-step="${currentStep}"]`);
        if (currentStepElement) {
            currentStepElement.classList.add('active');
            currentStepElement.style.display = 'block';
        }
        
        // Update step indicators
        stepIndicators.forEach((indicator, index) => {
            indicator.classList.remove('active', 'completed');
            if (index + 1 === currentStep) {
                indicator.classList.add('active');
            } else if (index + 1 < currentStep) {
                indicator.classList.add('completed');
            }
        });
        
        // Update navigation buttons
        if (prevBtn) prevBtn.style.display = currentStep === 1 ? 'none' : 'flex';
        if (nextBtn) nextBtn.style.display = currentStep >= totalSteps ? 'none' : 'flex';
        if (submitBtn) submitBtn.style.display = currentStep >= totalSteps ? 'flex' : 'none';
        
        // Scroll to top of form
        const container = document.querySelector('.affiliate-form-container');
        if (container) {
            container.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
    
    // Validate current step
    function validateCurrentStep() {
        const currentStepElement = document.querySelector(`.affiliate-form-step[data-step="${currentStep}"]`);
        if (!currentStepElement) return true;
        
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
                showFieldError(field, 'Este campo é obrigatório');
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                hideFieldError(field);
                
                // Additional validation
                if (field.type === 'email' && !isValidEmail(field.value)) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    showFieldError(field, 'Por favor, insira um endereço de email válido');
                    isValid = false;
                }
                
                if (field.type === 'password' && field.value.length < 6) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    showFieldError(field, 'A senha deve ter pelo menos 6 caracteres');
                    isValid = false;
                }
            }
        });
        
        // Check radio buttons for prefix and account type
        if (currentStep === 2) {
            const prefixChecked = currentStepElement.querySelector('input[name="name_prefix"]:checked');
            const typeChecked = currentStepElement.querySelector('input[name="type"]:checked');
            
            if (!prefixChecked) {
                showMessage('Por favor, selecione um título', 'danger');
                isValid = false;
            }
            if (!typeChecked) {
                showMessage('Por favor, selecione um tipo de conta', 'danger');
                isValid = false;
            }
        }
        
        // Additional step-specific validations
        if (currentStep === 1) {
            const password = document.getElementById('affiliate_reg_password');
            const passwordConfirm = document.getElementById('affiliate_reg_password_confirm');
            const username = document.getElementById('affiliate_reg_username');
            
            if (username && username.value.length < 3) {
                username.classList.add('is-invalid');
                showFieldError(username, 'O nome de usuário deve ter pelo menos 3 caracteres');
                isValid = false;
            }
            
            if (password && passwordConfirm) {
                if (password.value !== passwordConfirm.value) {
                    passwordConfirm.classList.add('is-invalid');
                    showFieldError(passwordConfirm, 'As senhas não coincidem');
                    isValid = false;
                } else if (password.value && passwordConfirm.value) {
                    passwordConfirm.classList.remove('is-invalid');
                    passwordConfirm.classList.add('is-valid');
                    hideFieldError(passwordConfirm);
                }
            }
        }
        
        // Step 4 validation - state field only required if country is selected
        if (currentStep === 4) {
            const countryField = currentStepElement.querySelector('select[name="country"]');
            const stateField = currentStepElement.querySelector('select[name="state"]');
            
            if (countryField && countryField.value && stateField && stateField.value === '' && !stateField.disabled) {
                stateField.classList.add('is-invalid');
                showFieldError(stateField, 'Please select a state/province');
                isValid = false;
            }
        }
        
        // Step 5 validation - terms and conditions checkbox
        if (currentStep === 5) {
            const termsCheckbox = currentStepElement.querySelector('input[name="terms_conditions"]');
            const termsContainer = currentStepElement.querySelector('.affiliate-terms-checkbox');
            
            if (termsCheckbox && !termsCheckbox.checked) {
                if (termsContainer) {
                    termsContainer.classList.add('error');
                }
                showMessage('Você deve concordar com os Termos e Condições para prosseguir', 'danger');
                isValid = false;
                
                // Focus the checkbox
                termsCheckbox.focus();
            } else if (termsCheckbox && termsCheckbox.checked) {
                if (termsContainer) {
                    termsContainer.classList.remove('error');
                }
            }
        }
        
        return isValid;
    }
    
    // Add terms checkbox interactivity
    const termsCheckbox = document.querySelector('input[name="terms_conditions"]');
    if (termsCheckbox) {
        termsCheckbox.addEventListener('change', function() {
            const termsContainer = this.closest('.affiliate-terms-checkbox');
            if (this.checked && termsContainer) {
                termsContainer.classList.remove('error');
            }
        });
    }
    
    // Initialize form
    updateForm();
}

function initFormValidation() {
    // Real-time validation for all form inputs
    const inputs = document.querySelectorAll('.affiliate-form-control');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            // Remove invalid state while typing
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
                hideFieldError(this);
            }
        });
    });
}

function validateField(field) {
    let isValid = true;
    
    // Required field validation
    if (field.hasAttribute('required') && !field.value.trim()) {
        field.classList.add('is-invalid');
        showFieldError(field, 'Este campo é obrigatório');
        return false;
    }
    
    // Email validation
    if (field.type === 'email' && field.value && !isValidEmail(field.value)) {
        field.classList.add('is-invalid');
        showFieldError(field, 'Por favor, insira um endereço de email válido');
        return false;
    }
    
    // Password validation
    if (field.type === 'password' && field.value && field.value.length < 6) {
        field.classList.add('is-invalid');
        showFieldError(field, 'A senha deve ter pelo menos 6 caracteres');
        return false;
    }
    
    // Username validation
    if (field.id === 'affiliate_reg_username' && field.value && field.value.length < 3) {
        field.classList.add('is-invalid');
        showFieldError(field, 'O nome de usuário deve ter pelo menos 3 caracteres');
        return false;
    }
    
    // Password confirmation validation
    if (field.id === 'affiliate_reg_password_confirm') {
        const password = document.getElementById('affiliate_reg_password');
        if (password && field.value && password.value !== field.value) {
            field.classList.add('is-invalid');
            showFieldError(field, 'As senhas não coincidem');
            return false;
        }
    }
    
    // Mobile number validation
    if (field.name === 'mobile_number' && field.value && !/^\d{10,15}$/.test(field.value.replace(/\D/g, ''))) {
        field.classList.add('is-invalid');
        showFieldError(field, 'Por favor, insira um número de celular válido');
        return false;
    }
    
    // If we reach here, field is valid
    field.classList.remove('is-invalid');
    field.classList.add('is-valid');
    hideFieldError(field);
    return true;
}

function showFieldError(field, message) {
    hideFieldError(field); // Remove existing error
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'affiliate-invalid-feedback';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function hideFieldError(field) {
    const existingError = field.parentNode.querySelector('.affiliate-invalid-feedback');
    if (existingError) {
        existingError.remove();
    }
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Utility functions for enhanced UX - Universal button loading state
function showLoading(button) {
    if (!button) return function() {};
    
    const originalText = button.innerHTML;
    
    // Use CSS classes instead of inline styles to prevent size changes
    button.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="animation: spin 1s linear infinite; margin-right: 8px;"><circle cx="12" cy="12" r="3" fill="currentColor"/></svg>Processing...';
    button.disabled = true;
    button.classList.add('loading');
    
    return function hideLoading() {
        button.innerHTML = originalText;
        button.disabled = false;
        button.classList.remove('loading');
    };
}

function showMessage(message, type) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.affiliate-message-alert');
    existingMessages.forEach(msg => msg.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} affiliate-message-alert`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()" style="float: right; background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
    `;
    
    // Insert at the top of the form container
    const container = document.querySelector('.affiliate-form-container') || document.querySelector('.affiliate-dashboard');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
    }
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Enhanced mobile experience
function initMobileEnhancements() {
    // Prevent zoom on input focus for iOS
    if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                input.style.fontSize = '16px';
            });
            input.addEventListener('blur', function() {
                input.style.fontSize = '';
            });
        });
    }
}

// Initialize mobile enhancements
initMobileEnhancements();

// Comprehensive country-state data for immediate functionality
const simpleCountriesData = {
    "United States": {
        "states": [
            "Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", "Delaware", 
            "District of Columbia", "Florida", "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", 
            "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", "Michigan", "Minnesota", 
            "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", "New Jersey", 
            "New Mexico", "New York", "North Carolina", "North Dakota", "Ohio", "Oklahoma", "Oregon", 
            "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", 
            "Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming"
        ]
    },
    "Canada": {
        "states": [
            "Alberta", "British Columbia", "Manitoba", "New Brunswick", "Newfoundland and Labrador", 
            "Northwest Territories", "Nova Scotia", "Nunavut", "Ontario", "Prince Edward Island", 
            "Quebec", "Saskatchewan", "Yukon"
        ]
    },
    "Australia": {
        "states": [
            "Australian Capital Territory", "New South Wales", "Northern Territory", "Queensland", 
            "South Australia", "Tasmania", "Victoria", "Western Australia"
        ]
    },
    "United Kingdom": {
        "states": [
            "England", "Scotland", "Wales", "Northern Ireland"
        ]
    },
    "Germany": {
        "states": [
            "Baden-Württemberg", "Bavaria", "Berlin", "Brandenburg", "Bremen", "Hamburg", "Hesse", 
            "Lower Saxony", "Mecklenburg-Vorpommern", "North Rhine-Westphalia", "Rhineland-Palatinate", 
            "Saarland", "Saxony", "Saxony-Anhalt", "Schleswig-Holstein", "Thuringia"
        ]
    },
    "France": {
        "states": [
            "Auvergne-Rhône-Alpes", "Bourgogne-Franche-Comté", "Brittany", "Centre-Val de Loire", 
            "Corsica", "Grand Est", "Hauts-de-France", "Île-de-France", "Normandy", "Nouvelle-Aquitaine", 
            "Occitania", "Pays de la Loire", "Provence-Alpes-Côte d'Azur"
        ]
    },
    "Italy": {
        "states": [
            "Abruzzo", "Basilicata", "Calabria", "Campania", "Emilia-Romagna", "Friuli-Venezia Giulia", 
            "Lazio", "Liguria", "Lombardy", "Marche", "Molise", "Piedmont", "Apulia", "Sardinia", 
            "Sicily", "Tuscany", "Trentino-Alto Adige", "Umbria", "Valle d'Aosta", "Veneto"
        ]
    },
    "Spain": {
        "states": [
            "Andalusia", "Aragon", "Asturias", "Balearic Islands", "Basque Country", "Canary Islands", 
            "Cantabria", "Castile and León", "Castilla-La Mancha", "Catalonia", "Community of Madrid", 
            "Extremadura", "Galicia", "La Rioja", "Murcia", "Navarre", "Valencia"
        ]
    },
    "Netherlands": {
        "states": [
            "Drenthe", "Flevoland", "Friesland", "Gelderland", "Groningen", "Limburg", "North Brabant", 
            "North Holland", "Overijssel", "South Holland", "Utrecht", "Zeeland"
        ]
    },
    "Belgium": {
        "states": [
            "Brussels-Capital Region", "Flemish Region", "Walloon Region"
        ]
    },
    "Switzerland": {
        "states": [
            "Aargau", "Appenzell Ausserrhoden", "Appenzell Innerrhoden", "Basel-Landschaft", "Basel-Stadt", 
            "Bern", "Fribourg", "Geneva", "Glarus", "Grisons", "Jura", "Lucerne", "Neuchâtel", "Nidwalden", 
            "Obwalden", "Schaffhausen", "Schwyz", "Solothurn", "St. Gallen", "Thurgau", "Ticino", "Uri", 
            "Valais", "Vaud", "Zug", "Zurich"
        ]
    },
    "Austria": {
        "states": [
            "Burgenland", "Carinthia", "Lower Austria", "Upper Austria", "Salzburg", "Styria", 
            "Tyrol", "Vorarlberg", "Vienna"
        ]
    },
    "Sweden": {
        "states": [
            "Blekinge", "Dalarna", "Gävleborg", "Gotland", "Halland", "Jämtland", "Jönköping", "Kalmar", 
            "Kronoberg", "Norrbotten", "Örebro", "Östergötland", "Skåne", "Södermanland", "Stockholm", 
            "Uppsala", "Värmland", "Västerbotten", "Västernorrland", "Västmanland", "Västra Götaland"
        ]
    },
    "Norway": {
        "states": [
            "Agder", "Innlandet", "Møre og Romsdal", "Nordland", "Oslo", "Rogaland", 
            "Troms og Finnmark", "Trøndelag", "Vestfold og Telemark", "Vestland", "Viken"
        ]
    },
    "Denmark": {
        "states": [
            "Capital Region of Denmark", "Central Denmark Region", "North Denmark Region", 
            "Region Zealand", "Region of Southern Denmark"
        ]
    },
    "Finland": {
        "states": [
            "Lapland", "North Ostrobothnia", "Kainuu", "North Karelia", "Northern Savonia", "Southern Savonia", 
            "South Karelia", "Kymenlaakso", "Päijät-Häme", "Tavastia Proper", "Pirkanmaa", "Satakunta", 
            "Ostrobothnia", "South Ostrobothnia", "Central Ostrobothnia", "Central Finland", "Uusimaa", 
            "Southwest Finland", "Åland"
        ]
    },
    "Poland": {
        "states": [
            "Greater Poland", "Kuyavian-Pomeranian", "Lesser Poland", "Lodz", "Lower Silesian", "Lublin", 
            "Lubusz", "Masovian", "Opole", "Podlaskie", "Pomeranian", "Silesian", "Subcarpathian", 
            "Swietokrzyskie", "Warmian-Masurian", "West Pomeranian"
        ]
    },
    "Czech Republic": {
        "states": [
            "Prague", "Central Bohemian", "South Bohemian", "Plzen", "Karlovy Vary", "Usti nad Labem", 
            "Liberec", "Hradec Kralove", "Pardubice", "Vysocina", "South Moravian", "Olomouc", 
            "Zlin", "Moravian-Silesian"
        ]
    },
    "Hungary": {
        "states": [
            "Budapest", "Pest", "Fejer", "Komarom-Esztergom", "Veszprem", "Gyor-Moson-Sopron", "Vas", 
            "Zala", "Baranya", "Somogy", "Tolna", "Bacs-Kiskun", "Bekes", "Csongrad", "Hajdu-Bihar", 
            "Jasz-Nagykun-Szolnok", "Szabolcs-Szatmar-Bereg", "Borsod-Abauj-Zemplen", "Heves", "Nograd"
        ]
    },
    "Romania": {
        "states": [
            "Alba", "Arad", "Arges", "Bacau", "Bihor", "Bistrita-Nasaud", "Botosani", "Brasov", "Braila", 
            "Buzau", "Caras-Severin", "Calarasi", "Cluj", "Constanta", "Covasna", "Dambovita", "Dolj", 
            "Galati", "Giurgiu", "Gorj", "Harghita", "Hunedoara", "Ialomita", "Iasi", "Ilfov", "Maramures", 
            "Mehedinti", "Mures", "Neamt", "Olt", "Prahova", "Salaj", "Satu Mare", "Sibiu", "Suceava", 
            "Teleorman", "Timis", "Tulcea", "Vaslui", "Valcea", "Vrancea", "Bucharest"
        ]
    },
    "Portugal": {
        "states": [
            "Aveiro", "Beja", "Braga", "Bragança", "Castelo Branco", "Coimbra", "Évora", "Faro", "Guarda", 
            "Leiria", "Lisboa", "Portalegre", "Porto", "Santarém", "Setúbal", "Viana do Castelo", 
            "Vila Real", "Viseu", "Azores", "Madeira"
        ]
    },
    "Greece": {
        "states": [
            "Attica", "Central Greece", "Central Macedonia", "Crete", "Eastern Macedonia and Thrace", 
            "Epirus", "Ionian Islands", "North Aegean", "Peloponnese", "South Aegean", "Thessaly", 
            "Western Greece", "Western Macedonia"
        ]
    },
    "Ireland": {
        "states": [
            "Carlow", "Cavan", "Clare", "Cork", "Donegal", "Dublin", "Galway", "Kerry", "Kildare", 
            "Kilkenny", "Laois", "Leitrim", "Limerick", "Longford", "Louth", "Mayo", "Meath", "Monaghan", 
            "Offaly", "Roscommon", "Sligo", "Tipperary", "Waterford", "Westmeath", "Wexford", "Wicklow", 
            "Antrim", "Armagh", "Down", "Fermanagh", "Londonderry", "Tyrone"
        ]
    },
    "Singapore": {
        "states": [
            "Central Region", "East Region", "North Region", "North-East Region", "West Region"
        ]
    },
    "Malaysia": {
        "states": [
            "Johor", "Kedah", "Kelantan", "Malacca", "Negeri Sembilan", "Pahang", "Penang", "Perak", 
            "Perlis", "Sabah", "Sarawak", "Selangor", "Terengganu", "Kuala Lumpur", "Putrajaya", "Labuan"
        ]
    },
        "India": {
            "states": [
                "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh", "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jharkhand",
                "Karnataka", "Kerala", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab",
                "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal",
                "Andaman and Nicobar Islands", "Chandigarh", "Dadra and Nagar Haveli and Daman and Diu", "Delhi", "Jammu and Kashmir", "Ladakh", "Lakshadweep", "Puducherry"
            ]
        },
        "China": {
            "states": [
                "Beijing", "Tianjin", "Hebei", "Shanxi", "Inner Mongolia", "Liaoning", "Jilin", "Heilongjiang",
                "Shanghai", "Jiangsu", "Zhejiang", "Anhui", "Fujian", "Jiangxi", "Shandong", "Henan",
                "Hubei", "Hunan", "Guangdong", "Guangxi", "Hainan", "Chongqing", "Sichuan", "Guizhou",
                "Yunnan", "Tibet", "Shaanxi", "Gansu", "Qinghai", "Ningxia", "Xinjiang", "Hong Kong", "Macau", "Taiwan"
            ]
        },
    "Japan": {
        "states": [
            "Hokkaido", "Aomori", "Iwate", "Miyagi", "Akita", "Yamagata", "Fukushima", "Ibaraki",
            "Tochigi", "Gunma", "Saitama", "Chiba", "Tokyo", "Kanagawa", "Niigata", "Toyama",
            "Ishikawa", "Fukui", "Yamanashi", "Nagano", "Gifu", "Shizuoka", "Aichi", "Mie",
            "Shiga", "Kyoto", "Osaka", "Hyogo", "Nara", "Wakayama", "Tottori", "Shimane",
            "Okayama", "Hiroshima", "Yamaguchi", "Tokushima", "Kagawa", "Ehime", "Kochi", "Fukuoka",
            "Saga", "Nagasaki", "Kumamoto", "Oita", "Miyazaki", "Kagoshima", "Okinawa"
        ]
    },
    "South Korea": {
        "states": [
            "Seoul", "Busan", "Daegu", "Incheon", "Gwangju", "Daejeon", "Ulsan", "Sejong",
            "Gyeonggi", "Gangwon", "North Chungcheong", "South Chungcheong", "North Jeolla", "South Jeolla",
            "North Gyeongsang", "South Gyeongsang", "Jeju"
        ]
    },
    "Thailand": {
        "states": [
            "Bangkok", "Amnat Charoen", "Ang Thong", "Bueng Kan", "Buriram", "Chachoengsao", "Chai Nat", 
            "Chaiyaphum", "Chanthaburi", "Chiang Mai", "Chiang Rai", "Chonburi", "Chumphon", "Kalasin", 
            "Kamphaeng Phet", "Kanchanaburi", "Khon Kaen", "Krabi", "Lampang", "Lamphun", "Loei", "Lopburi", 
            "Mae Hong Son", "Maha Sarakham", "Mukdahan", "Nakhon Nayok", "Nakhon Pathom", "Nakhon Phanom", 
            "Nakhon Ratchasima", "Nakhon Sawan", "Nakhon Si Thammarat", "Nan", "Narathiwat", "Nongbua Lamphu", 
            "Nong Khai", "Nonthaburi", "Pathum Thani", "Pattani", "Phangnga", "Phatthalung", "Phayao", 
            "Phetchabun", "Phetchaburi", "Phichit", "Phitsanulok", "Phra Nakhon Si Ayutthaya", "Phrae", 
            "Phuket", "Prachinburi", "Prachuap Khiri Khan", "Ranong", "Ratchaburi", "Rayong", "Roi Et", 
            "Sa Kaeo", "Sakon Nakhon", "Samut Prakan", "Samut Sakhon", "Samut Songkhram", "Saraburi", 
            "Satun", "Sing Buri", "Sisaket", "Songkhla", "Sukhothai", "Suphan Buri", "Surat Thani", 
            "Surin", "Tak", "Trang", "Trat", "Ubon Ratchathani", "Udon Thani", "Uthai Thani", "Uttaradit", 
            "Yala", "Yasothon"
        ]
    },
    "Philippines": {
        "states": [
            "Ilocos Norte", "Ilocos Sur", "La Union", "Pangasinan", "Batanes", "Cagayan", "Isabela", 
            "Nueva Vizcaya", "Quirino", "Aurora", "Bataan", "Bulacan", "Nueva Ecija", "Pampanga", "Tarlac", 
            "Zambales", "Batangas", "Cavite", "Laguna", "Quezon", "Rizal", "Marinduque", "Occidental Mindoro", 
            "Oriental Mindoro", "Palawan", "Romblon", "Albay", "Camarines Norte", "Camarines Sur", 
            "Catanduanes", "Masbate", "Sorsogon", "Abra", "Apayao", "Benguet", "Ifugao", "Kalinga", 
            "Mountain Province", "Aklan", "Antique", "Capiz", "Guimaras", "Iloilo", "Negros Occidental", 
            "Bohol", "Cebu", "Negros Oriental", "Siquijor", "Biliran", "Eastern Samar", "Leyte", 
            "Northern Samar", "Samar", "Southern Leyte", "Zamboanga del Norte", "Zamboanga del Sur", 
            "Zamboanga Sibugay", "Bukidnon", "Camiguin", "Lanao del Norte", "Misamis Occidental", 
            "Misamis Oriental", "Davao de Oro", "Davao del Norte", "Davao del Sur", "Davao Occidental", 
            "Davao Oriental", "Cotabato", "Sarangani", "South Cotabato", "Sultan Kudarat", "Agusan del Norte", 
            "Agusan del Sur", "Dinagat Islands", "Surigao del Norte", "Surigao del Sur", "Basilan", 
            "Lanao del Sur", "Maguindanao del Norte", "Maguindanao del Sur", "Sulu", "Tawi-Tawi"
        ]
    },
    "Indonesia": {
        "states": [
            "Aceh", "North Sumatra", "West Sumatra", "Riau", "Riau Islands", "Jambi", "South Sumatra", 
            "Bangka Belitung Islands", "Bengkulu", "Lampung", "Jakarta Special Capital Region", "Banten", 
            "West Java", "Central Java", "Yogyakarta Special Region", "East Java", "West Kalimantan", 
            "Central Kalimantan", "South Kalimantan", "East Kalimantan", "North Kalimantan", "North Sulawesi", 
            "Central Sulawesi", "South Sulawesi", "Southeast Sulawesi", "West Sulawesi", "Gorontalo", "Bali", 
            "West Nusa Tenggara", "East Nusa Tenggara", "Maluku", "North Maluku", "Papua", "West Papua", 
            "Southwest Papua", "South Papua", "Central Papua", "Highland Papua"
        ]
    },
    "Vietnam": {
        "states": [
            "Hanoi", "Ho Chi Minh City", "Da Nang", "Hai Phong", "Can Tho", "An Giang", "Ba Ria-Vung Tau", 
            "Bac Giang", "Bac Kan", "Bac Lieu", "Bac Ninh", "Ben Tre", "Binh Dinh", "Binh Duong", 
            "Binh Phuoc", "Binh Thuan", "Ca Mau", "Cao Bang", "Dak Lak", "Dak Nong", "Dien Bien", 
            "Dong Nai", "Dong Thap", "Gia Lai", "Ha Giang", "Ha Nam", "Ha Tinh", "Hai Duong", "Hau Giang", 
            "Hoa Binh", "Hung Yen", "Khanh Hoa", "Kien Giang", "Kon Tum", "Lai Chau", "Lam Dong", 
            "Lang Son", "Lao Cai", "Long An", "Nam Dinh", "Nghe An", "Ninh Binh", "Ninh Thuan", 
            "Phu Tho", "Phu Yen", "Quang Binh", "Quang Nam", "Quang Ngai", "Quang Ninh", "Quang Tri", 
            "Soc Trang", "Son La", "Tay Ninh", "Thai Binh", "Thai Nguyen", "Thanh Hoa", "Thua Thien Hue", 
            "Tien Giang", "Tra Vinh", "Tuyen Quang", "Vinh Long", "Vinh Phuc", "Yen Bai"
        ]
    },
    "New Zealand": {
        "states": [
            "Northland", "Auckland", "Waikato", "Bay of Plenty", "Gisborne", "Hawke's Bay", "Taranaki", 
            "Manawatu-Whanganui", "Wellington", "Tasman", "Nelson", "Marlborough", "West Coast", 
            "Canterbury", "Otago", "Southland"
        ]
    },
    "Chile": {
        "states": [
            "Arica y Parinacota", "Tarapacá", "Antofagasta", "Atacama", "Coquimbo", "Valparaíso", 
            "Metropolitan Region", "O'Higgins", "Maule", "Ñuble", "Biobío", "Araucanía", "Los Ríos", 
            "Los Lagos", "Aysén", "Magallanes y Antártica Chilena"
        ]
    },
    "Turkey": {
        "states": [
            "Adana", "Adıyaman", "Afyonkarahisar", "Ağrı", "Aksaray", "Amasya", "Ankara", "Antalya", 
            "Ardahan", "Artvin", "Aydın", "Balıkesir", "Bartın", "Batman", "Bayburt", "Bilecik", 
            "Bingöl", "Bitlis", "Bolu", "Burdur", "Bursa", "Çanakkale", "Çankırı", "Çorum", "Denizli", 
            "Diyarbakır", "Düzce", "Edirne", "Elazığ", "Erzincan", "Erzurum", "Eskişehir", "Gaziantep", 
            "Giresun", "Gümüşhane", "Hakkâri", "Hatay", "Iğdır", "Isparta", "Istanbul", "İzmir", 
            "Kahramanmaraş", "Karabük", "Karaman", "Kars", "Kastamonu", "Kayseri", "Kırıkkale", 
            "Kırklareli", "Kırşehir", "Kilis", "Kocaeli", "Konya", "Kütahya", "Malatya", "Manisa", 
            "Mardin", "Mersin", "Muğla", "Muş", "Nevşehir", "Niğde", "Ordu", "Osmaniye", "Rize", 
            "Sakarya", "Samsun", "Siirt", "Sinop", "Sivas", "Şanlıurfa", "Şırnak", "Tekirdağ", 
            "Tokat", "Trabzon", "Tunceli", "Uşak", "Van", "Yalova", "Yozgat", "Zonguldak"
        ]
    },
    "Israel": {
        "states": [
            "Northern District", "Haifa District", "Central District", "Tel Aviv District", 
            "Jerusalem District", "Southern District"
        ]
    },
    "United Arab Emirates": {
        "states": [
            "Abu Dhabi", "Dubai", "Sharjah", "Ajman", "Umm Al Quwain", "Ras Al Khaimah", "Fujairah"
        ]
    },
    "Saudi Arabia": {
        "states": [
            "Riyadh Province", "Makkah Province", "Eastern Province", "Asir Province", "Jazan Province", 
            "Madinah Province", "Al-Qassim Province", "Ha'il Province", "Tabuk Province", 
            "Northern Borders Province", "Najran Province", "Al Bahah Province", "Al Jawf Province"
        ]
    },
    "Brazil": {
        "states": [
            "Acre", "Alagoas", "Amapá", "Amazonas", "Bahia", "Ceará", "Distrito Federal", "Espírito Santo", "Goiás", "Maranhão",
            "Mato Grosso", "Mato Grosso do Sul", "Minas Gerais", "Pará", "Paraíba", "Paraná", "Pernambuco", "Piauí", "Rio de Janeiro", "Rio Grande do Norte",
            "Rio Grande do Sul", "Rondônia", "Roraima", "Santa Catarina", "São Paulo", "Sergipe", "Tocantins"
        ]
    },                
    "Mexico": {
        "states": [
            "Aguascalientes", "Baja California", "Baja California Sur", "Campeche", "Chiapas", "Chihuahua", "Coahuila", "Colima", "Durango", "Guanajuato",
            "Guerrero", "Hidalgo", "Jalisco", "México", "Michoacán", "Morelos", "Nayarit", "Nuevo León", "Oaxaca", "Puebla",
            "Querétaro", "Quintana Roo", "San Luis Potosí", "Sinaloa", "Sonora", "Tabasco", "Tamaulipas", "Tlaxcala", "Veracruz", "Yucatán", "Zacatecas", "Mexico City"
        ]
    },
    "Argentina": {
        "states": [
            "Buenos Aires", "Catamarca", "Chaco", "Chubut", "Córdoba", "Corrientes", "Entre Ríos", "Formosa", "Jujuy", "La Pampa",
            "La Rioja", "Mendoza", "Misiones", "Neuquén", "Río Negro", "Salta", "San Juan", "San Luis", "Santa Cruz", "Santa Fe",
            "Santiago del Estero", "Tierra del Fuego", "Tucumán", "Ciudad Autónoma de Buenos Aires"
        ]
    },
    "South Africa": {
        "states": [
            "Eastern Cape", "Free State", "Gauteng", "KwaZulu-Natal", "Limpopo", "Mpumalanga", "Northern Cape", "North West", "Western Cape"
        ]
    },
    "Egypt": {
        "states": [
            "Alexandria", "Aswan", "Asyut", "Beheira", "Beni Suef", "Cairo", "Dakahlia", "Damietta", 
            "Faiyum", "Gharbia", "Giza", "Ismailia", "Kafr el-Sheikh", "Luxor", "Matruh", "Minya", 
            "Monufia", "New Valley", "North Sinai", "Port Said", "Qalyubia", "Qena", "Red Sea", 
            "Sharqia", "Sohag", "South Sinai", "Suez"
        ]
    }
};

// Global countries data storage
let globalCountriesData = {};

function loadCountriesData() {
    // Try to load from WordPress first, then fallback to simple data
    if (typeof affiliate_ajax !== 'undefined' && affiliate_ajax.countries_data) {
        globalCountriesData = affiliate_ajax.countries_data;
        console.log('Countries data loaded from WordPress:', Object.keys(globalCountriesData).length, 'countries');
    } else {
        console.log('Using fallback countries data');
        globalCountriesData = simpleCountriesData;
    }
    
    // Initialize country dropdown functionality
    setTimeout(() => {
        initCountryDropdown();
    }, 100); // Small delay to ensure DOM is ready
}

function initCountryDropdown() {
    const countrySelect = document.getElementById('country');
    if (!countrySelect) {
        console.log('Country select element not found - may not be on registration page');
        return;
    }
    
    console.log('Found country select element, adding change event listener');
    console.log('Available countries in globalCountriesData:', Object.keys(globalCountriesData));
    
    // Add event listener for country selection
    countrySelect.addEventListener('change', function() {
        const selectedCountry = this.value;
        const stateSelect = document.getElementById('state');
        
        console.log('Country selected:', selectedCountry);
        console.log('State select found:', !!stateSelect);
        
        if (!stateSelect) {
            console.error('State select element not found!');
            return;
        }
        
        if (selectedCountry && globalCountriesData[selectedCountry]) {
            const states = globalCountriesData[selectedCountry].states;
            console.log(`Found states for ${selectedCountry}:`, states ? states.length : 'No states');
            
            if (states && states.length > 0) {
                // Clear existing options
                stateSelect.innerHTML = '<option value="">Selecionar Estado/Província</option>';
                
                // Add states/provinces
                states.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state;
                    option.textContent = state;
                    stateSelect.appendChild(option);
                });
                
                stateSelect.disabled = false;
                stateSelect.removeAttribute('disabled');
                stateSelect.setAttribute('required', 'required');
                console.log(`Successfully loaded ${states.length} states for ${selectedCountry}`);
            } else {
                // No states available, disable select
                stateSelect.innerHTML = '<option value="">No states available</option>';
                stateSelect.disabled = true;
                stateSelect.removeAttribute('required');
                console.log(`No states available for ${selectedCountry}`);
            }
        } else {
            // Reset state dropdown
            stateSelect.innerHTML = '<option value="">Select Country First</option>';
            stateSelect.disabled = true;
            stateSelect.removeAttribute('required');
            console.log('Resetting state dropdown - no country selected or no data available');
        }
    });
    
    console.log('Country change listener added successfully');
}

function loadStates(countryId) {
    console.log('loadStates called for country ID:', countryId);
    
    const stateSelect = document.getElementById('state');
    if (!stateSelect) {
        console.log('State select element not found');
        return;
    }
    
    // Check if states data is available
    if (!window.AffiliateCountriesData || !window.AffiliateCountriesData.states) {
        console.error('States data not loaded');
        return;
    }
    
    const states = window.AffiliateCountriesData.states[countryId];
    console.log('Found states for country', countryId, ':', states);
    
    stateSelect.innerHTML = '<option value="">Select State/Province</option>';
    stateSelect.disabled = false;
    
    if (states && states.length > 0) {
        states.forEach(state => {
            const option = document.createElement('option');
            option.value = state.name;
            option.textContent = state.name;
            stateSelect.appendChild(option);
        });
        console.log('States loaded successfully:', states.length, 'states');
    } else {
        stateSelect.innerHTML = '<option value="">No states available</option>';
        stateSelect.disabled = true;
        console.log('No states found for country ID:', countryId);
    }
}

function resetStates() {
    const stateSelect = document.getElementById('state');
    if (stateSelect) {
        stateSelect.innerHTML = '<option value="">Select Country First</option>';
        stateSelect.disabled = true;
    }
}

// Add loadAffiliateTypes stub function (not needed since affiliate types are hardcoded in form)
function loadAffiliateTypes() {
    // Affiliate types are already hardcoded in the form HTML, no action needed
    console.log('Affiliate types already loaded from HTML');
}

// Admin functionality
function initAdminFunctionality() {
    // Admin login form
    const adminLoginForm = document.getElementById('affiliateAdminLoginForm');
    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', handleAdminLogin);
    }
    
    // Load admin applications if on admin dashboard
    if (document.getElementById('applications-table')) {
        loadApplications();
        loadEmailConfig();
    }
}

function handleAdminLogin(e) {
    e.preventDefault();
    
    const username = document.getElementById('admin_username').value;
    const password = document.getElementById('admin_password').value;
    
    if (!username || !password) {
        showMessage('Por favor, preencha todos os campos', 'danger');
        return;
    }
    
    const button = e.target.querySelector('button[type="submit"]');
    const hideLoading = showLoading(button);
    
    const formData = new FormData();
    formData.append('action', 'affiliate_admin_login');
    formData.append('admin_username', username);
    formData.append('admin_password', password);
    formData.append('nonce', affiliate_ajax.nonce);
    
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            showMessage(data.data.message, 'success');
            setTimeout(() => {
                window.location.href = data.data.redirect;
            }, 1000);
        } else {
            showMessage(data.data || 'Falha no login', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showMessage('Ocorreu um erro. Tente novamente.', 'danger');
        console.error('Admin login error:', error);
    });
}

// Pagination variables
let currentPage = 1;
let totalPages = 1;
let totalRecords = 0;
let currentPerPage = 25;

function loadApplications(page = 1) {
    currentPage = page;
    const statusFilter = document.getElementById('status-filter')?.value || '';
    const perPage = document.getElementById('per-page-filter')?.value || 25;
    currentPerPage = parseInt(perPage);
    
    console.log('Loading applications with status filter:', statusFilter, 'page:', page, 'per page:', perPage);
    
    const formData = new FormData();
    formData.append('action', 'affiliate_get_applications');
    formData.append('status', statusFilter);
    formData.append('page', page);
    formData.append('per_page', perPage);
    formData.append('nonce', affiliate_ajax.nonce);
    
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            populateApplicationsTable(data.data.applications);
            updateStatistics(data.data.stats);
            updatePagination(data.data.pagination);
        } else {
            showMessage('Failed to load applications', 'danger');
            console.error('Failed to load applications:', data.data);
        }
    })
    .catch(error => {
        showMessage('Error loading applications', 'danger');
        console.error('Load applications error:', error);
    });
}

function populateApplicationsTable(applications) {
    const tbody = document.getElementById('applications-tbody');
    if (!tbody) return;
    
    if (applications.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No applications found</td></tr>';
        return;
    }
    
    tbody.innerHTML = applications.map(app => `
        <tr>
            <td>${app.name_prefix || ''} ${app.first_name} ${app.last_name}</td>
            <td>${app.email}</td>
            <td>${app.company_name || 'N/A'}</td>
            <td>${app.affiliate_type}</td>
            <td>${app.country}</td>
            <td><span class="status-badge ${app.status.replace(/\s+/g, '-').replace('awaiting-approval', 'awaiting')}">${app.status}</span></td>
            <td>${new Date(app.created_at).toLocaleDateString()}</td>
            <td>
                <button type="button" class="action-btn view" onclick="showRegistrationDetailsModal(${app.id})" style="margin-right: 5px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                    </svg>
                    View
                </button>
                <button type="button" class="action-btn edit" onclick="showStatusModal(${app.id}, '${app.status}', '${app.admin_remarks || ''}')">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z"/>
                    </svg>
                    Edit
                </button>
            </td>
        </tr>
    `).join('');
}

function updateStatistics(stats) {
    console.log('Updating statistics:', stats);
    document.getElementById('pending-count').textContent = stats.pending || 0;
    document.getElementById('approved-count').textContent = stats.approved || 0;
    document.getElementById('rejected-count').textContent = stats.rejected || 0;
}

function showStatusModal(applicationId, currentStatus, currentRemarks) {
    document.getElementById('update_application_id').value = applicationId;
    document.getElementById('new_status').value = currentStatus;
    document.getElementById('admin_remarks').value = currentRemarks;
    document.getElementById('statusUpdateModal').style.display = 'flex';
}

function hideStatusModal() {
    document.getElementById('statusUpdateModal').style.display = 'none';
}

// Registration Details Modal Functions
function showRegistrationDetailsModal(applicationId) {
    const modal = document.getElementById('registrationDetailsModal');
    const content = document.getElementById('registrationDetailsContent');
    
    // Show modal and loading state
    modal.style.display = 'block';
    content.innerHTML = '<div style="text-align: center; padding: 40px;"><div class="loading-spinner"></div><p>Loading registration details...</p></div>';
    
    // Fetch registration details
    const formData = new FormData();
    formData.append('action', 'affiliate_get_registration_details');
    formData.append('application_id', applicationId);
    formData.append('nonce', affiliate_ajax.nonce);
    
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayRegistrationDetails(data.data);
        } else {
            content.innerHTML = '<div style="text-align: center; padding: 40px; color: #dc3545;"><p>Failed to load registration details</p></div>';
        }
    })
    .catch(error => {
        console.error('Error loading registration details:', error);
        content.innerHTML = '<div style="text-align: center; padding: 40px; color: #dc3545;"><p>Error loading registration details</p></div>';
    });
}

function hideRegistrationDetailsModal() {
    document.getElementById('registrationDetailsModal').style.display = 'none';
}

function displayRegistrationDetails(user) {
    const content = document.getElementById('registrationDetailsContent');
    
    const mobileDisplay = user.mobile_number ? 
        (user.country_code && !user.mobile_number.startsWith('+') ? 
            `+${user.country_code} ${user.mobile_number}` : user.mobile_number) : 'Not provided';
    
    const addressParts = [
        user.address_line1,
        user.address_line2,
        user.city,
        user.country,
        user.zipcode
    ].filter(part => part && part.trim() !== '');
    
    const fullAddress = addressParts.length > 0 ? addressParts.join(', ') : 'Not provided';
    
    content.innerHTML = `
        <div class="registration-detail-grid">
            <div class="registration-detail-item">
                <div class="registration-detail-label">Username</div>
                <div class="registration-detail-value">${user.username || 'Not provided'}</div>
            </div>
            <div class="registration-detail-item">
                <div class="registration-detail-label">Full Name</div>
                <div class="registration-detail-value">${(user.name_prefix ? user.name_prefix + ' ' : '') + user.first_name + ' ' + user.last_name}</div>
            </div>
            <div class="registration-detail-item">
                <div class="registration-detail-label">Email Address</div>
                <div class="registration-detail-value">${user.email}</div>
            </div>
            <div class="registration-detail-item">
                <div class="registration-detail-label">Date of Birth</div>
                <div class="registration-detail-value">${user.dob || 'Not provided'}</div>
            </div>
            <div class="registration-detail-item">
                <div class="registration-detail-label">Account Type</div>
                <div class="registration-detail-value">${user.type}</div>
            </div>
            ${user.company_name ? `
            <div class="registration-detail-item">
                <div class="registration-detail-label">Company Name</div>
                <div class="registration-detail-value">${user.company_name}</div>
            </div>
            ` : ''}
            <div class="registration-detail-item">
                <div class="registration-detail-label">Mobile Number</div>
                <div class="registration-detail-value">${mobileDisplay}</div>
            </div>
            <div class="registration-detail-item">
                <div class="registration-detail-label">Complete Address</div>
                <div class="registration-detail-value">${fullAddress}</div>
            </div>
            <div class="registration-detail-item">
                <div class="registration-detail-label">Affiliate Type</div>
                <div class="registration-detail-value">${user.affiliate_type || 'Not provided'}</div>
            </div>
            <div class="registration-detail-item">
                <div class="registration-detail-label">Preferred Currency</div>
                <div class="registration-detail-value">${user.currency || 'Not provided'}</div>
            </div>
            ${user.chat_id_channel ? `
            <div class="registration-detail-item">
                <div class="registration-detail-label">Chat ID/Channel</div>
                <div class="registration-detail-value">${user.chat_id_channel}</div>
            </div>
            ` : ''}
            <div class="registration-detail-item">
                <div class="registration-detail-label">Security Question</div>
                <div class="registration-detail-value">${user.security_que || 'Not provided'}</div>
            </div>
            <div class="registration-detail-item">
                <div class="registration-detail-label">Security Answer</div>
                <div class="registration-detail-value">${user.security_ans || 'Not provided'}</div>
            </div>
            <div class="registration-detail-item">
                <div class="registration-detail-label">Registration Date</div>
                <div class="registration-detail-value">${new Date(user.created_at).toLocaleString()}</div>
            </div>
            <div class="registration-detail-item">
                <div class="registration-detail-label">Current Status</div>
                <div class="registration-detail-value">
                    <span class="status-badge ${user.status.replace(/\s+/g, '-').replace('awaiting-approval', 'awaiting')}">${user.status}</span>
                </div>
            </div>
            ${user.admin_remarks ? `
            <div class="registration-detail-item" style="grid-column: 1 / -1; border-left: 4px solid #17a2b8; background: #e7f3ff;">
                <div class="registration-detail-label" style="color: #0c5460;">Admin Remarks</div>
                <div class="registration-detail-value" style="color: #0c5460; font-style: italic;">${user.admin_remarks.replace(/\n/g, '<br>')}</div>
            </div>
            ` : ''}
        </div>
    `;
}

// Pagination Functions
function updatePagination(pagination) {
    totalPages = pagination.total_pages;
    totalRecords = pagination.total_records;
    currentPage = pagination.current_page;
    
    const paginationContainer = document.getElementById('pagination-container');
    const paginationInfo = document.getElementById('pagination-info-text');
    
    if (totalRecords === 0) {
        paginationContainer.style.display = 'none';
        return;
    }
    
    paginationContainer.style.display = 'flex';
    
    // Update pagination info
    const startRecord = ((currentPage - 1) * currentPerPage) + 1;
    const endRecord = Math.min(currentPage * currentPerPage, totalRecords);
    paginationInfo.textContent = `Showing ${startRecord} to ${endRecord} of ${totalRecords} entries`;
    
    // Update navigation buttons
    document.getElementById('first-page').disabled = currentPage === 1;
    document.getElementById('prev-page').disabled = currentPage === 1;
    document.getElementById('next-page').disabled = currentPage === totalPages;
    document.getElementById('last-page').disabled = currentPage === totalPages;
    
    // Generate page numbers
    generatePageNumbers();
}

function generatePageNumbers() {
    const numbersContainer = document.getElementById('pagination-numbers');
    numbersContainer.innerHTML = '';
    
    // Calculate page range to show
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    // Adjust start page if we're near the end
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    // Add ellipsis and first page if needed
    if (startPage > 1) {
        addPageNumber(1);
        if (startPage > 2) {
            addEllipsis();
        }
    }
    
    // Add visible page numbers
    for (let i = startPage; i <= endPage; i++) {
        addPageNumber(i);
    }
    
    // Add ellipsis and last page if needed
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            addEllipsis();
        }
        addPageNumber(totalPages);
    }
}

function addPageNumber(pageNum) {
    const numbersContainer = document.getElementById('pagination-numbers');
    const pageButton = document.createElement('button');
    pageButton.className = 'pagination-number' + (pageNum === currentPage ? ' active' : '');
    pageButton.textContent = pageNum;
    pageButton.onclick = () => goToPage(pageNum);
    numbersContainer.appendChild(pageButton);
}

function addEllipsis() {
    const numbersContainer = document.getElementById('pagination-numbers');
    const ellipsis = document.createElement('span');
    ellipsis.className = 'pagination-number';
    ellipsis.textContent = '...';
    ellipsis.style.cursor = 'default';
    ellipsis.style.border = 'none';
    ellipsis.style.background = 'transparent';
    numbersContainer.appendChild(ellipsis);
}

function goToPage(page) {
    if (page >= 1 && page <= totalPages && page !== currentPage) {
        loadApplications(page);
    }
}

function goToPreviousPage() {
    if (currentPage > 1) {
        goToPage(currentPage - 1);
    }
}

function goToNextPage() {
    if (currentPage < totalPages) {
        goToPage(currentPage + 1);
    }
}

function goToLastPage() {
    goToPage(totalPages);
}

function showEmailConfig() {
    document.getElementById('emailConfigModal').style.display = 'flex';
    loadEmailConfig();
}

function hideEmailConfig() {
    document.getElementById('emailConfigModal').style.display = 'none';
}

function loadEmailConfig() {
    const formData = new FormData();
    formData.append('action', 'affiliate_get_email_config');
    formData.append('nonce', affiliate_ajax.nonce);
    
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const config = data.data;
            document.getElementById('notification_emails').value = config.notification_emails || '';
            document.getElementById('from_email').value = config.from_email || '';
            document.getElementById('from_name').value = config.from_name || '';
        }
    })
    .catch(error => {
        console.error('Load email config error:', error);
    });
}

function adminLogout() {
    const formData = new FormData();
    formData.append('action', 'affiliate_admin_logout');
    formData.append('nonce', affiliate_ajax.nonce);
    
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Logged out successfully', 'success');
            setTimeout(() => {
                window.location.href = '/admin-login/';
            }, 1000);
        } else {
            showMessage('Logout failed', 'danger');
        }
    })
    .catch(error => {
        console.error('Logout error:', error);
        showMessage('Error during logout', 'danger');
    });
}

// Status update form submission
document.addEventListener('DOMContentLoaded', function() {
    const statusForm = document.getElementById('statusUpdateForm');
    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'affiliate_update_status');
            formData.append('nonce', affiliate_ajax.nonce);
            
            fetch(affiliate_ajax.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Status updated successfully', 'success');
                    hideStatusModal();
                    // Refresh the applications table to show updated status
                    setTimeout(() => {
                        loadApplications();
                    }, 500);
                } else {
                    showMessage(data.data || 'Failed to update status', 'danger');
                }
            })
            .catch(error => {
                showMessage('Error updating status', 'danger');
            });
        });
    }
    
    const emailConfigForm = document.getElementById('emailConfigForm');
    if (emailConfigForm) {
        emailConfigForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'affiliate_admin_update_email_config');
            formData.append('nonce', affiliate_ajax.nonce);
            
            fetch(affiliate_ajax.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Email configuration updated successfully', 'success');
                    hideEmailConfig();
                } else {
                    showMessage(data.data || 'Failed to update email configuration', 'danger');
                }
            })
            .catch(error => {
                showMessage('Error updating email configuration', 'danger');
            });
        });
    }
});

// Initialize when DOM is ready - Secondary initialization block  
// (Note: Main DOMContentLoaded listener is at the top of the file)
function initializeAffiliatePortal() {
    console.log('Initializing affiliate portal...');
    
    // Initialize countries data loading
    loadCountriesData();
    
    // Initialize form steps if on registration page
    if (document.querySelector('.affiliate-step')) {
        console.log('Initializing form steps...');
        // Form steps already initialized by initMultiStepForm()
    }
    
    // Initialize login form if present
    if (document.getElementById('affiliate-login-form')) {
        console.log('Initializing login form...');
        initializeLoginForm();
    }
    
    // Initialize dashboard features if present
    if (document.querySelector('.affiliate-dashboard-enhanced')) {
        console.log('Initializing dashboard...');
        initializeDashboard();
    }
    
    // Initialize admin dashboard if present
    if (document.querySelector('.affiliate-admin-dashboard')) {
        console.log('Initializing admin dashboard...');
        loadApplications();
    }
    
    console.log('Affiliate portal initialization complete');
}

// CRITICAL: Enhanced logout with browser cache clearing (Portuguese)
function affiliateLogout() {
    if (confirm('Tem certeza que deseja sair?')) {
        // CRITICAL: Clear browser cache and storage before logout
        try {
            // Clear localStorage
            localStorage.clear();
            
            // Clear sessionStorage
            sessionStorage.clear();
            
            // Clear any cached user data
            if (typeof caches !== 'undefined') {
                caches.keys().then(function(names) {
                    names.forEach(function(name) {
                        caches.delete(name);
                    });
                });
            }
        } catch (e) {
            console.log('Cache clearing error (non-critical):', e);
        }
        
        const formData = new FormData();
        formData.append('action', 'affiliate_logout');
        formData.append('nonce', affiliate_ajax.nonce);
        
        fetch(affiliate_ajax.ajax_url, {
            method: 'POST',
            body: formData,
            cache: 'no-cache', // Force no cache
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Logout realizado com sucesso!', 'success');
                // CRITICAL: Force page refresh with no cache and proper cookie clearing time
                setTimeout(() => {
                    // Add cache-busting parameter
                    const redirectUrl = data.data.redirect || '/afiliado-login';
                    const cacheBuster = '?cb=' + Date.now();
                    
                    // Clear any remaining browser data
                    document.cookie.split(";").forEach(function(c) { 
                        document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
                    });
                    
                    window.location.href = redirectUrl + cacheBuster;
                }, 1500); // Increased delay to ensure server-side cookie clearing completes
            } else {
                showMessage(data.data || 'Falha no logout', 'danger');
            }
        })
        .catch(error => {
            console.error('Logout error:', error);
            showMessage('Falha no logout', 'danger');
        });
    }
}

// Modern animations and interactions
function initModernAnimations() {
    // Add hover effects to form controls
    const formControls = document.querySelectorAll('.affiliate-form-control');
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.closest('.affiliate-form-group').classList.add('focused');
        });
        
        control.addEventListener('blur', function() {
            this.closest('.affiliate-form-group').classList.remove('focused');
        });
    });
    
    // Add step animation when changing registration steps
    const steps = document.querySelectorAll('.affiliate-form-step');
    steps.forEach((step, index) => {
        if (step.style.display !== 'none') {
            step.style.animation = 'affiliateFadeInScale 0.5s ease-out';
        }
    });
    
    // Enhanced button interactions
    const buttons = document.querySelectorAll('.affiliate-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Create ripple effect
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('span');
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

// Also try loading countries immediately if DOM is already ready
if (document.readyState !== 'loading') {
    console.log('DOM already ready, initializing immediately...');
    loadCountries();
    
    if (document.querySelector('.affiliate-step')) {
        // Form steps already initialized by initMultiStepForm()
    }
    
    if (document.getElementById('affiliate-login-form')) {
        initializeLoginForm();
    }
    
    if (document.querySelector('.affiliate-dashboard-enhanced')) {
        initializeDashboard();
    }
}