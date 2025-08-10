/**
 * Mortgage Calculator Module for Laravel
 * Adapted from WordPress Houzez theme
 */
(function($) {
    'use strict';

    // Utility functions
    const util = {
        /**
         * Parse number from input element
         */
        parseNumberInput: function(selector) {
            const value = $(selector).val();
            return parseFloat(value.replace(/[^0-9.-]/g, '')) || 0;
        },

        /**
         * Calculate monthly mortgage payment
         */
        calculateMonthlyPayment: function(principal, annualInterestRate, loanTermInYears) {
            if (annualInterestRate === 0) {
                return principal / (loanTermInYears * 12);
            }
            
            const monthlyInterestRate = annualInterestRate / 100 / 12;
            const numberOfPayments = loanTermInYears * 12;
            
            const monthlyPayment = principal * 
                (monthlyInterestRate * Math.pow(1 + monthlyInterestRate, numberOfPayments)) /
                (Math.pow(1 + monthlyInterestRate, numberOfPayments) - 1);
            
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
            const homePrice = util.parseNumberInput('#homePrice');
            const downPaymentPercentage = util.parseNumberInput('#downPaymentPercentage');
            const annualInterestRate = util.parseNumberInput('#annualInterestRate');
            const loanTermInYears = util.parseNumberInput('#loanTermInYears');
            const annualPropertyTaxRate = util.parseNumberInput('#annualPropertyTaxRate');
            const annualHomeInsurance = util.parseNumberInput('#annualHomeInsurance');
            const monthlyHOAFees = util.parseNumberInput('#monthlyHOAFees');
            const pmi = util.parseNumberInput('#pmi');

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
            $('#downPaymentResult').html(util.currencyFormat(formattedDownPayment));
            $('#loadAmountResult').html(util.currencyFormat(formattedLoanAmount));
            $('#monthlyMortgagePaymentResult').html(util.currencyFormat(formattedMonthlyPayment));
            $('#monthlyPropertyTaxResult').html(util.currencyFormat(formattedPropertyTax));
            $('#monthlyHomeInsuranceResult').html(util.currencyFormat(formattedHomeInsurance));

            if (pmiRequired && $('#monthlyPMIResult').length > 0) {
                $('.rslt-pmi').show();
                $('#monthlyPMIResult').html(util.currencyFormat(formattedPMI));
            } else {
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

            this.updateChart(chartData);
        },

        /**
         * Update the mortgage donut chart
         * @param {Array} chartData - Chart data for visualization
         */
        updateChart: function(chartData) {
            const canvas = document.getElementById('mortgage-calculator-chart');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');

            if (window.myChart) {
                window.myChart.destroy();
            }

            // Filter out zero values
            const filteredData = chartData.filter(item => item.value > 0);

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
        },

        /**
         * Initialize the mortgage calculator
         */
        init: function() {
            const self = this;
            
            // If the calculator form exists, set up event handlers
            if ($('#houzez-calculator-form').length > 0) {
                $('#houzez-calculator-form input').on('input', function() {
                    self.mortgageCalculation();
                });

                // Calculate on page load
                setTimeout(function() {
                    self.mortgageCalculation();
                }, 100);
            }
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        MortgageCalculator.init();
    });

    // Also initialize on window load as fallback
    $(window).on('load', function() {
        if ($('#houzez-calculator-form').length > 0 && !window.myChart) {
            MortgageCalculator.init();
        }
    });

})(jQuery);
