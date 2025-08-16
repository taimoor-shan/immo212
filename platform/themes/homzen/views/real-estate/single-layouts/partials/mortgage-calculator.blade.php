@php
    // Get property price - use a default if not available
    $property_price = $property->price_numeric ?? 500000;

    // Get current application currency
    $current_currency = get_application_currency();
    $currency_symbol = $current_currency ? $current_currency->symbol : 'USD';
    $currency_title = $current_currency ? $current_currency->title : 'USD';
    
    // Default calculator settings with sensible defaults for Morocco
    $mcal_terms = theme_option('mcal_terms', 25); // Default 25 years
    $mcal_down_payment = theme_option('mcal_down_payment', 20); // Default 20%
    $mcal_interest_rate = theme_option('mcal_interest_rate', 4.5); // Default 4.5%
    $mcal_prop_tax = theme_option('mcal_prop_tax', 1.2); // Default 1.2%
    $mcal_hi = theme_option('mcal_hi', 5000); // Default 5000 MAD annual
    $mcal_hoa = theme_option('mcal_hoa', 0); // Default 0 MAD monthly
    $mcal_pmi = theme_option('mcal_pmi', 0.5); // Default 0.5%
    
    // Enable/disable features - enable all by default for full functionality
    $mcal_prop_tax_enable = theme_option('mcal_prop_tax_enable', true);
    $mcal_hi_enable = theme_option('mcal_hi_enable', true);
    $mcal_hoa_enable = theme_option('mcal_hoa_enable', true); // Enable HOA
    $mcal_pmi_enable = theme_option('mcal_pmi_enable', true);
@endphp

<div class="single-property-element {{ $class ?? '' }}">
    <div class="h7 title">{{ __('Mortgage Calculator') }}</div>
    
    <div class="mortgage-calculator-container">
        <div class="d-flex align-items-center flex-column flex-lg-row gap-4">
            <!-- Chart Section -->
            <div class="mortgage-calculator-chart d-flex align-items-center mb-4" role="complementary">
                <div class="mortgage-calculator-monthly-payment-wrap w-100 text-center">
                    <div id="m_monthly_val" class="mortgage-calculator-monthly-payment mb-1"></div>
                    <div class="mortgage-calculator-monthly-requency">{{ __('Monthly') }}</div>
                </div>
                <canvas id="mortgage-calculator-chart" class="m-auto" width="250" height="250"></canvas>
            </div>

            <!-- Data Display Section -->
            <div class="mortgage-calculator-data w-100 mb-4 ms-0 ms-lg-5" role="complementary">
                <ul class="list-unstyled list-lined" role="list">
                    <li class="mortgage-calculator-data-1 d-flex align-items-center justify-content-between stats-data-1" role="listitem">
                        <div class="list-lined-item w-100 d-flex justify-content-between py-2">	
                            <span>
                                <i class="ti ti-badge me-1" aria-hidden="true"></i> 
                                <strong>{{ __('Down Payment') }}</strong> 
                            </span>
                            <span id="downPaymentResult"></span>
                        </div>
                    </li>

                    <li class="mortgage-calculator-data-1 d-flex align-items-center justify-content-between stats-data-01" role="listitem">
                        <div class="list-lined-item w-100 d-flex justify-content-between py-2">	
                            <span>
                                <i class="ti ti-badge me-1" aria-hidden="true"></i> 
                                <strong>{{ __('Loan Amount') }}</strong> 
                            </span>
                            <span id="loadAmountResult"></span>
                        </div>
                    </li>

                    <li class="mortgage-calculator-data-1 d-flex align-items-center justify-content-between stats-data-1" role="listitem">
                        <div class="list-lined-item w-100 d-flex justify-content-between py-2">	
                            <span>
                                <i class="ti ti-badge me-1" aria-hidden="true"></i> 
                                <strong>{{ __('Monthly Mortgage Payment') }}</strong> 
                            </span>
                            <span id="monthlyMortgagePaymentResult"></span>
                        </div>
                    </li>

                    @if($mcal_prop_tax_enable)
                    <li class="mortgage-calculator-data-2 d-flex align-items-center justify-content-between stats-data-2" role="listitem">
                        <div class="list-lined-item w-100 d-flex justify-content-between py-2">	
                            <span>
                                <i class="ti ti-badge me-1" aria-hidden="true"></i> 
                                <strong>{{ __('Property Tax') }}</strong> 
                            </span>
                            <span id="monthlyPropertyTaxResult"></span>
                        </div>
                    </li>
                    @endif

                    @if($mcal_hi_enable)
                    <li class="mortgage-calculator-data-3 d-flex align-items-center justify-content-between stats-data-3" role="listitem">
                        <div class="list-lined-item w-100 d-flex justify-content-between py-2">	
                            <span>
                                <i class="ti ti-badge me-1" aria-hidden="true"></i> 
                                <strong>{{ __('Home Insurance') }}</strong> 
                            </span>
                            <span id="monthlyHomeInsuranceResult"></span>
                        </div>
                    </li>
                    @endif

                    @if($mcal_pmi_enable)
                    <li class="mortgage-calculator-data-4 d-flex align-items-center justify-content-between stats-data-4 rslt-pmi" role="listitem">
                        <div class="list-lined-item w-100 d-flex justify-content-between py-2">	
                            <span>
                                <i class="ti ti-badge me-1" aria-hidden="true"></i> 
                                <strong>{{ __('PMI') }}</strong> 
                            </span>
                            <span id="monthlyPMIResult"></span>
                        </div>
                    </li>
                    @endif

                    @if($mcal_hoa_enable)
                    <li class="mortgage-calculator-data-5 d-flex align-items-center justify-content-between stats-data-5" role="listitem">
                        <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                            <span>
                                <i class="ti ti-badge me-1" aria-hidden="true"></i> 
                                <strong>{{ __('Monthly HOA Fees') }}</strong> 
                            </span>
                            <span id="monthlyHOAResult"></span>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Calculator Form -->
        <form id="houzez-calculator-form" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="homePrice" class="form-label">{{ __('Total Amount') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="homePrice" 
                                   placeholder="{{ __('Total Amount') }}" 
                                   value="{{ intval($property_price) }}">
                            <span class="input-group-text" aria-hidden="true">{{ $currency_symbol }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label" for="downPaymentPercentage">{{ __('Down Payment') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="downPaymentPercentage" 
                                   placeholder="{{ __('Down Payment') }}" 
                                   value="{{ $mcal_down_payment }}">
                            <span class="input-group-text" aria-hidden="true">%</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label" for="annualInterestRate">{{ __('Interest Rate') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="annualInterestRate" 
                                   placeholder="{{ __('Interest Rate') }}" 
                                   value="{{ $mcal_interest_rate }}">
                            <span class="input-group-text" aria-hidden="true">%</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label" for="loanTermInYears">{{ __('Loan Terms (Years)') }}</label>
                        <div class="input-group">
                            <input id="loanTermInYears" type="text" class="form-control" 
                                   placeholder="{{ __('Loan Terms (Years)') }}" 
                                   value="{{ $mcal_terms }}">
                            <span class="input-group-text" aria-hidden="true">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#6c757d" d="M19 4h-2V3a1 1 0 0 0-2 0v1H9V3a1 1 0 0 0-2 0v1H5a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3m1 15a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-7h16Zm0-9H4V7a1 1 0 0 1 1-1h2v1a1 1 0 0 0 2 0V6h6v1a1 1 0 0 0 2 0V6h2a1 1 0 0 1 1 1Z"/></svg>
                            </span>
                        </div>
                    </div>
                </div>

                @if($mcal_prop_tax_enable)
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label" for="annualPropertyTaxRate">{{ __('Annual Property Tax Rate') }}</label>
                        <div class="input-group">
                            <input id="annualPropertyTaxRate" type="text" class="form-control" 
                                   placeholder="{{ __('Property Tax') }}" 
                                   value="{{ $mcal_prop_tax }}">
                            <span class="input-group-text" aria-hidden="true">%</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($mcal_hi_enable)
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label" for="annualHomeInsurance">{{ __('Annual Home Insurance') }}</label>
                        <div class="input-group">
                            <input id="annualHomeInsurance" type="text" class="form-control" 
                                   placeholder="{{ __('Home Insurance') }}" 
                                   value="{{ $mcal_hi }}">
                            <span class="input-group-text" aria-hidden="true">{{ $currency_symbol }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($mcal_hoa_enable)
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label" for="monthlyHOAFees">{{ __('Monthly HOA Fees') }}</label>
                        <div class="input-group">
                            <input id="monthlyHOAFees" type="text" class="form-control" 
                                   placeholder="{{ __('Monthly HOA Fees') }}" 
                                   value="{{ $mcal_hoa }}">
                            <span class="input-group-text" aria-hidden="true">{{ $currency_symbol }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($mcal_pmi_enable)
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label" for="pmi">{{ __('PMI') }}</label>
                        <div class="input-group">
                            <input id="pmi" type="text" class="form-control" 
                                   placeholder="{{ __('PMI') }}" 
                                   value="{{ $mcal_pmi }}">
                            <span class="input-group-text" aria-hidden="true">%</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            {{-- Hidden fields for disabled options to ensure JS can still read them --}}
            @if(!$mcal_prop_tax_enable)
                <input type="hidden" id="annualPropertyTaxRate" value="0">
            @endif
            @if(!$mcal_hi_enable)
                <input type="hidden" id="annualHomeInsurance" value="0">
            @endif
            @if(!$mcal_hoa_enable)
                <input type="hidden" id="monthlyHOAFees" value="0">
            @endif
            @if(!$mcal_pmi_enable)
                <input type="hidden" id="pmi" value="0">
            @endif
        </form>
    </div>
</div>

{{-- Load Chart.js and calculator script directly --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
(function() {
    'use strict';
    
    console.log('Mortgage Calculator: Inline script starting...');
    
    // Wait for jQuery and Chart.js to be ready
    function initCalculator() {
        if (typeof jQuery === 'undefined' || typeof Chart === 'undefined') {
            console.log('Waiting for dependencies...', {jQuery: typeof jQuery !== 'undefined', Chart: typeof Chart !== 'undefined'});
            setTimeout(initCalculator, 100);
            return;
        }
        
        console.log('Dependencies ready, initializing calculator...');
        
        var $ = jQuery;
        
        // Utility functions
        function parseNumberInput(selector) {
            var element = $(selector);
            if (!element.length) {
                console.warn('Element not found:', selector);
                return 0;
            }
            var value = element.val();
            var parsed = parseFloat((value + '').replace(/[^0-9.-]/g, ''));
            return isNaN(parsed) ? 0 : parsed;
        }
        
        function calculateMonthlyPayment(principal, annualInterestRate, loanTermInYears) {
            if (principal <= 0 || loanTermInYears <= 0) {
                return 0;
            }
            
            if (annualInterestRate === 0) {
                return principal / (loanTermInYears * 12);
            }
            
            var monthlyInterestRate = annualInterestRate / 100 / 12;
            var numberOfPayments = loanTermInYears * 12;
            
            var monthlyPayment = principal * 
                (monthlyInterestRate * Math.pow(1 + monthlyInterestRate, numberOfPayments)) /
                (Math.pow(1 + monthlyInterestRate, numberOfPayments) - 1);
            
            return monthlyPayment;
        }
        
        function numberFormat(number, decimals) {
            if (isNaN(number) || number < 0) {
                return '0';
            }

            @php
                $decimalSeparator = setting('real_estate_decimal_separator', '.');
                if ($decimalSeparator == 'space') $decimalSeparator = ' ';

                $thousandSeparator = setting('real_estate_thousands_separator', ',');
                if ($thousandSeparator == 'space') $thousandSeparator = ' ';

                $currencyDecimals = $current_currency ? $current_currency->decimals : 2;
            @endphp

            var decimalPlaces = decimals ? {{ $currencyDecimals ?: 2 }} : 0;
            var decimalSeparator = '{{ $decimalSeparator }}';
            var thousandSeparator = '{{ $thousandSeparator }}';

            // Format the number with proper decimals
            var formatted = number.toFixed(decimalPlaces);

            // Split into integer and decimal parts
            var parts = formatted.split('.');
            var integerPart = parts[0];
            var decimalPart = parts[1];

            // Add thousand separators to integer part
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);

            // Combine parts with proper decimal separator
            if (decimalPlaces > 0 && decimalPart) {
                return integerPart + decimalSeparator + decimalPart;
            }

            return integerPart;
        }
        
        function currencyFormat(value) {
            @if($current_currency && $current_currency->is_prefix_symbol)
                @php
                    $space = setting('real_estate_add_space_between_price_and_currency', 0) == 1 ? ' ' : '';
                @endphp
                return '{{ $currency_symbol }}{{ $space }}' + value;
            @else
                @php
                    $space = setting('real_estate_add_space_between_price_and_currency', 0) == 1 ? ' ' : '';
                @endphp
                return value + '{{ $space }}{{ $currency_symbol }}';
            @endif
        }
        
        var myChart = null;
        
        function updateChart(chartData) {
            var canvas = document.getElementById('mortgage-calculator-chart');
            if (!canvas) {
                console.error('Canvas not found');
                return;
            }
            
            var ctx = canvas.getContext('2d');
            
            if (myChart) {
                myChart.destroy();
            }
            
            var filteredData = chartData.filter(function(item) { return item.value > 0; });
            
            myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: filteredData.map(function(item) { return item.label; }),
                    datasets: [{
                        data: filteredData.map(function(item) { return item.value; }),
                        backgroundColor: filteredData.map(function(item) { return item.color; }),
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '85%',
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = numberFormat(context.parsed, true);
                                    return label + ': ' + currencyFormat(value);
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function calculateMortgage() {
            console.log('Calculating mortgage...');
            
            var homePrice = parseNumberInput('#homePrice');
            var downPaymentPercentage = parseNumberInput('#downPaymentPercentage');
            var annualInterestRate = parseNumberInput('#annualInterestRate');
            var loanTermInYears = parseNumberInput('#loanTermInYears');
            var annualPropertyTaxRate = parseNumberInput('#annualPropertyTaxRate');
            var annualHomeInsurance = parseNumberInput('#annualHomeInsurance');
            var monthlyHOAFees = parseNumberInput('#monthlyHOAFees');
            var pmiRate = parseNumberInput('#pmi');
            
            console.log('Input values:', {
                homePrice: homePrice,
                downPaymentPercentage: downPaymentPercentage,
                annualInterestRate: annualInterestRate,
                loanTermInYears: loanTermInYears,
                pmiRate: pmiRate
            });
            
            // Calculate basic values
            var downPayment = homePrice * (downPaymentPercentage / 100);
            var principal = homePrice - downPayment;
            var monthlyPayment = calculateMonthlyPayment(principal, annualInterestRate, loanTermInYears);
            var monthlyPropertyTax = (homePrice * (annualPropertyTaxRate / 100)) / 12;
            var monthlyHomeInsurance = annualHomeInsurance / 12;
            
            // PMI calculation - Fixed logic
            var pmiRequired = downPaymentPercentage < 20;
            var monthlyPMI = 0;
            
            if (pmiRequired && pmiRate > 0 && principal > 0) {
                // PMI is calculated on the loan amount (principal) annually, then divided by 12
                monthlyPMI = (principal * (pmiRate / 100)) / 12;
            }
            
            console.log('PMI calculation:', {
                pmiRequired: pmiRequired,
                pmiRate: pmiRate,
                principal: principal,
                monthlyPMI: monthlyPMI
            });
            
            var totalMonthlyPayment = monthlyPayment + monthlyPropertyTax + monthlyHomeInsurance + monthlyHOAFees + monthlyPMI;
            
            // Update UI elements
            $('#downPaymentResult').html(currencyFormat(numberFormat(downPayment, true)));
            $('#loadAmountResult').html(currencyFormat(numberFormat(principal, true)));
            $('#monthlyMortgagePaymentResult').html(currencyFormat(numberFormat(monthlyPayment, true)));
            $('#monthlyPropertyTaxResult').html(currencyFormat(numberFormat(monthlyPropertyTax, true)));
            $('#monthlyHomeInsuranceResult').html(currencyFormat(numberFormat(monthlyHomeInsurance, true)));
            $('#monthlyHOAResult').html(currencyFormat(numberFormat(monthlyHOAFees, true)));
            $('#m_monthly_val').html(currencyFormat(numberFormat(totalMonthlyPayment, true)));
            
            // Handle PMI display - Show/hide entire row based on down payment requirement
            var pmiElement = $('.rslt-pmi');
            var pmiEnabled = $('#pmi').length > 0 && !$('#pmi').is(':hidden');
            
            // Only show PMI row if PMI is enabled AND down payment is less than 20%
            if (pmiEnabled && pmiRequired) {
                pmiElement.show();
                $('#monthlyPMIResult').html(currencyFormat(numberFormat(monthlyPMI, true)));
                console.log('PMI row shown - Down payment:', downPaymentPercentage + '%', 'PMI amount:', monthlyPMI);
            } else {
                pmiElement.hide();
                console.log('PMI row hidden - Down payment:', downPaymentPercentage + '%', 'PMI Required:', pmiRequired);
            }
            
            // Update chart data
            var chartData = [
                { label: 'Mortgage Payment', value: monthlyPayment, color: '#ff6384' }
            ];
            
            if (monthlyPropertyTax > 0) {
                chartData.push({ label: 'Property Tax', value: monthlyPropertyTax, color: '#36a2eb' });
            }
            
            if (monthlyHomeInsurance > 0) {
                chartData.push({ label: 'Home Insurance', value: monthlyHomeInsurance, color: '#ffce56' });
            }
            
            if (monthlyHOAFees > 0) {
                chartData.push({ label: 'HOA Fees', value: monthlyHOAFees, color: '#c2d500' });
            }
            
            // Only add PMI to chart if it's actually required and being charged
            if (pmiRequired && monthlyPMI > 0) {
                chartData.push({ label: 'PMI', value: monthlyPMI, color: '#4bc0c0' });
            }
            
            updateChart(chartData);
        }
        
        // Set up event handlers with debouncing
        var calculationTimeout;
        function debouncedCalculation() {
            clearTimeout(calculationTimeout);
            calculationTimeout = setTimeout(calculateMortgage, 300);
        }
        
        $('#houzez-calculator-form input').on('input keyup change', debouncedCalculation);
        
        // Initial calculation
        setTimeout(calculateMortgage, 500);
        
        console.log('Mortgage Calculator: Initialization complete');
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCalculator);
    } else {
        initCalculator();
    }
})();
</script>

{{-- Inline CSS for mortgage calculator --}}
<style>
/* Mortgage Calculator Styles */
.mortgage-calculator-container {
    padding: 20px 0;
}

.mortgage-calculator-chart {
    position: relative;
    min-height: 250px;
}

.mortgage-calculator-monthly-payment-wrap {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
}

.mortgage-calculator-monthly-payment {
    font-size: 24px;
    font-weight: 500;
    color: #1a1a1a;
}

.mortgage-calculator-monthly-requency {
    font-size: 14px;
    color: #666;
}

.mortgage-calculator-data .list-lined-item {
    border-bottom: 1px solid #e5e5e5;
    font-size: 14px;
}



.mortgage-calculator-data .list-lined-item i {
    color: #00BA74;
}

.mortgage-calculator-data-1 i { color: #ff6384; }
.mortgage-calculator-data-2 i { color: #36a2eb; }
.mortgage-calculator-data-3 i { color: #ffce56; }
.mortgage-calculator-data-4 i { color: #4bc0c0; }
.mortgage-calculator-data-5 i { color: #c2d500; }

#houzez-calculator-form .form-group {
    margin-bottom: 1rem;
}

#houzez-calculator-form .form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    font-size: 14px;
}

#houzez-calculator-form .input-group {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    width: 100%;
}

#houzez-calculator-form .input-group > .form-control {
    position: relative;
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
    margin-bottom:0;
}

#houzez-calculator-form .input-group-text {
    display: flex;
    align-items: center;
    justify-content:center;
    padding: 0.375rem 0.75rem;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.5;
    color: #6c757d;
    text-align: center;
    white-space: nowrap;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

#houzez-calculator-form .input-group > .input-group-text:first-child {
    border-radius: 0.25rem 0 0 0.25rem;
    border-right: 0;
    width: 86%;
}

#houzez-calculator-form .input-group > .input-group-text:last-child {
    border-radius: 0 0.25rem 0.25rem 0;
    border-left: 0;
    width: 14%;
    text-align:center;
}

#houzez-calculator-form .input-group > .form-control:not(:first-child) {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

#houzez-calculator-form .input-group > .form-control:not(:last-child) {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}
</style>
