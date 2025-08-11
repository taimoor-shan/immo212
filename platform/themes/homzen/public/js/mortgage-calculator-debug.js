/**
 * Mortgage Calculator Module for Laravel - DEBUG VERSION
 * Adapted from WordPress Houzez theme
 */
(function($) {
    'use strict';
    
    console.log('Mortgage Calculator Debug: Script loaded');
    
    // Wait for both jQuery and Chart.js to be available
    function waitForDependencies(callback) {
        var checkInterval = setInterval(function() {
            if (typeof $ !== 'undefined' && typeof Chart !== 'undefined') {
                clearInterval(checkInterval);
                console.log('Mortgage Calculator Debug: Dependencies ready (jQuery and Chart.js)');
                callback();
            } else {
                console.log('Mortgage Calculator Debug: Waiting for dependencies...', {
                    jQuery: typeof $ !== 'undefined',
                    Chart: typeof Chart !== 'undefined'
                });
            }
        }, 100);
    }

    // Utility functions
    const util = {
        /**
         * Parse number from input element
         */
        parseNumberInput: function(selector) {
            const element = $(selector);
            if (!element.length) {
                console.warn('Mortgage Calculator Debug: Element not found:', selector);
                return 0;
            }
            const value = element.val();
            const parsed = parseFloat((value + '').replace(/[^0-9.-]/g, '')) || 0;
            console.log('Mortgage Calculator Debug: Parsing', selector, 'value:', value, 'parsed:', parsed);
            return parsed;
        },

        /**
         * Calculate monthly mortgage payment
         */
        calculateMonthlyPayment: function(principal, annualInterestRate, loanTermInYears) {
            console.log('Mortgage Calculator Debug: Calculating payment', {
                principal: principal,
                annualInterestRate: annualInterestRate,
                loanTermInYears: loanTermInYears
            });
            
            if (annualInterestRate === 0) {
                return principal / (loanTermInYears * 12);
            }
            
            const monthlyInterestRate = annualInterestRate / 100 / 12;
            const numberOfPayments = loanTermInYears * 12;
            
            const monthlyPayment = principal * 
                (monthlyInterestRate * Math.pow(1 + monthlyInterestRate, numberOfPayments)) /
                (Math.pow(1 + monthlyInterestRate, numberOfPayments) - 1);
            
            console.log('Mortgage Calculator Debug: Monthly payment calculated:', monthlyPayment);
            return monthlyPayment;
        },

        /**
         * Format number with thousand separators
         */
        numberFormat: function(number, decimals = false) {
            if (decimals) {
                return number.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            return Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },

        /**
         * Format currency
         */
        currencyFormat: function(value) {
            return value + ' MAD';
        }
    };

    const MortgageCalculator = {
        /**
         * Calculation handler for mortgage calculator section
         */
        mortgageCalculation: function() {
            console.log('Mortgage Calculator Debug: Starting calculation...');
            
            const homePrice = util.parseNumberInput('#homePrice');
            const downPaymentPercentage = util.parseNumberInput('#downPaymentPercentage');
            const annualInterestRate = util.parseNumberInput('#annualInterestRate');
            const loanTermInYears = util.parseNumberInput('#loanTermInYears');
            const annualPropertyTaxRate = util.parseNumberInput('#annualPropertyTaxRate');
            const annualHomeInsurance = util.parseNumberInput('#annualHomeInsurance');
            const monthlyHOAFees = util.parseNumberInput('#monthlyHOAFees');
            const pmi = util.parseNumberInput('#pmi');

            console.log('Mortgage Calculator Debug: Input values:', {
                homePrice: homePrice,
                downPaymentPercentage: downPaymentPercentage,
                annualInterestRate: annualInterestRate,
                loanTermInYears: loanTermInYears,
                annualPropertyTaxRate: annualPropertyTaxRate,
                annualHomeInsurance: annualHomeInsurance,
                monthlyHOAFees: monthlyHOAFees,
                pmi: pmi
            });

            const downPayment = homePrice * (downPaymentPercentage / 100);
            const principal = homePrice - downPayment;
            const monthlyPayment = util.calculateMonthlyPayment(
                principal,
                annualInterestRate,
                loanTermInYears
            );
            const monthlyPropertyTax = (homePrice * (annualPropertyTaxRate / 100)) / 12;
            const monthlyHomeInsurance = annualHomeInsurance / 12;

            const pmiRequired = downPaymentPercentage < 20;
            const monthlyPMI = pmiRequired ? (principal * (pmi / 100)) / 12 : 0;

            const totalMonthlyPayment =
                monthlyPayment +
                monthlyPropertyTax +
                monthlyHomeInsurance +
                monthlyHOAFees +
                monthlyPMI;

            const loanAmount = homePrice - downPayment;

            console.log('Mortgage Calculator Debug: Calculated values:', {
                downPayment: downPayment,
                loanAmount: loanAmount,
                monthlyPayment: monthlyPayment,
                monthlyPropertyTax: monthlyPropertyTax,
                monthlyHomeInsurance: monthlyHomeInsurance,
                monthlyPMI: monthlyPMI,
                monthlyHOAFees: monthlyHOAFees,
                totalMonthlyPayment: totalMonthlyPayment
            });

            // Format values
            const formattedDownPayment = util.numberFormat(downPayment, true);
            const formattedLoanAmount = util.numberFormat(loanAmount, true);
            const formattedMonthlyPayment = util.numberFormat(monthlyPayment, true);
            const formattedPropertyTax = util.numberFormat(monthlyPropertyTax, true);
            const formattedHomeInsurance = util.numberFormat(monthlyHomeInsurance, true);
            const formattedPMI = pmiRequired ? util.numberFormat(monthlyPMI, true) : '';
            const formattedHOAFees = util.numberFormat(monthlyHOAFees, true);
            const formattedTotalMonthlyPayment = util.numberFormat(totalMonthlyPayment, true);

            // Update UI elements with results
            console.log('Mortgage Calculator Debug: Updating UI elements...');
            
            $('#downPaymentResult').html(util.currencyFormat(formattedDownPayment));
            $('#loadAmountResult').html(util.currencyFormat(formattedLoanAmount));
            $('#monthlyMortgagePaymentResult').html(util.currencyFormat(formattedMonthlyPayment));
            $('#monthlyPropertyTaxResult').html(util.currencyFormat(formattedPropertyTax));
            $('#monthlyHomeInsuranceResult').html(util.currencyFormat(formattedHomeInsurance));

            if (pmiRequired && $('#monthlyPMIResult').length > 0) {
                console.log('Mortgage Calculator Debug: Showing PMI');
                $('.rslt-pmi').show();
                $('#monthlyPMIResult').html(util.currencyFormat(formattedPMI));
            } else {
                console.log('Mortgage Calculator Debug: Hiding PMI');
                $('.rslt-pmi').hide();
            }

            if ($('#monthlyHOAResult').length > 0) {
                $('#monthlyHOAResult').html(util.currencyFormat(formattedHOAFees));
            }
            
            $('#m_monthly_val').html(util.currencyFormat(formattedTotalMonthlyPayment));

            // Update chart data for visualization
            const chartData = [
                {
                    label: 'Monthly Mortgage Payment',
                    value: monthlyPayment,
                    color: '#ff6384',
                },
                {
                    label: 'Property Tax',
                    value: monthlyPropertyTax,
                    color: '#36a2eb',
                },
                {
                    label: 'Home Insurance',
                    value: monthlyHomeInsurance,
                    color: '#ffce56',
                }
            ];

            if (monthlyHOAFees > 0) {
                chartData.push({
                    label: 'HOA',
                    value: monthlyHOAFees,
                    color: '#c2d500',
                });
            }

            if (pmiRequired && monthlyPMI > 0) {
                chartData.push({
                    label: 'PMI',
                    value: monthlyPMI,
                    color: '#4bc0c0',
                });
            }

            console.log('Mortgage Calculator Debug: Chart data:', chartData);
            this.updateChart(chartData);
        },

        /**
         * Update the mortgage donut chart
         * @param {Array} chartData - Chart data for visualization
         */
        updateChart: function(chartData) {
            console.log('Mortgage Calculator Debug: Updating chart...');
            
            const canvas = document.getElementById('mortgage-calculator-chart');
            if (!canvas) {
                console.error('Mortgage Calculator Debug: Canvas element not found!');
                return;
            }
            
            console.log('Mortgage Calculator Debug: Canvas found:', canvas);
            const ctx = canvas.getContext('2d');

            if (window.myChart) {
                console.log('Mortgage Calculator Debug: Destroying existing chart');
                window.myChart.destroy();
            }

            // Filter out zero values
            const filteredData = chartData.filter(item => item.value > 0);
            console.log('Mortgage Calculator Debug: Filtered chart data:', filteredData);

            try {
                window.myChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: filteredData.map(item => item.label),
                        datasets: [{
                            data: filteredData.map(item => item.value),
                            backgroundColor: filteredData.map(item => item.color),
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
                                        const label = context.label || '';
                                        const value = util.numberFormat(context.parsed, true);
                                        return label + ': ' + util.currencyFormat(value);
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Mortgage Calculator Debug: Chart created successfully');
            } catch (error) {
                console.error('Mortgage Calculator Debug: Error creating chart:', error);
            }
        },

        /**
         * Initialize the mortgage calculator
         */
        init: function() {
            console.log('Mortgage Calculator Debug: Initializing...');
            const self = this;
            
            // Check if the calculator form exists
            const form = $('#houzez-calculator-form');
            if (form.length > 0) {
                console.log('Mortgage Calculator Debug: Form found, setting up event handlers');
                
                // Log all input fields
                form.find('input').each(function() {
                    console.log('Mortgage Calculator Debug: Found input:', this.id, 'value:', $(this).val());
                });
                
                $('#houzez-calculator-form input').on('input', function() {
                    console.log('Mortgage Calculator Debug: Input changed:', this.id, 'new value:', $(this).val());
                    self.mortgageCalculation();
                });

                // Calculate on page load
                setTimeout(function() {
                    console.log('Mortgage Calculator Debug: Running initial calculation');
                    self.mortgageCalculation();
                }, 100);
            } else {
                console.error('Mortgage Calculator Debug: Form #houzez-calculator-form not found!');
            }
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        console.log('Mortgage Calculator Debug: Document ready');
        waitForDependencies(function() {
            MortgageCalculator.init();
        });
    });

    // Also initialize on window load as fallback
    $(window).on('load', function() {
        console.log('Mortgage Calculator Debug: Window loaded');
        if ($('#houzez-calculator-form').length > 0 && !window.myChart) {
            waitForDependencies(function() {
                MortgageCalculator.init();
            });
        }
    });

})(jQuery);
