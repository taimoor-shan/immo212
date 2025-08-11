/******/ (() => { // webpackBootstrap
/*!*****************************************************************!*\
  !*** ./platform/themes/homzen/assets/js/mortgage-calculator.js ***!
  \*****************************************************************/
/**
 * Mortgage Calculator Module for Laravel
 * Adapted from WordPress Houzez theme
 */
(function ($) {
  'use strict';

  // Utility functions
  var util = {
    /**
     * Parse number from input element
     */
    parseNumberInput: function parseNumberInput(selector) {
      var value = $(selector).val();
      return parseFloat(value.replace(/[^0-9.-]/g, '')) || 0;
    },
    /**
     * Calculate monthly mortgage payment
     */
    calculateMonthlyPayment: function calculateMonthlyPayment(principal, annualInterestRate, loanTermInYears) {
      if (annualInterestRate === 0) {
        return principal / (loanTermInYears * 12);
      }
      var monthlyInterestRate = annualInterestRate / 100 / 12;
      var numberOfPayments = loanTermInYears * 12;
      var monthlyPayment = principal * (monthlyInterestRate * Math.pow(1 + monthlyInterestRate, numberOfPayments)) / (Math.pow(1 + monthlyInterestRate, numberOfPayments) - 1);
      return monthlyPayment;
    },
    /**
     * Format number with thousand separators
     */
    numberFormat: function numberFormat(number) {
      var decimals = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      if (decimals) {
        return number.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
      }
      return Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    },
    /**
     * Format currency
     */
    currencyFormat: function currencyFormat(value) {
      return value + ' MAD';
    }
  };
  var MortgageCalculator = {
    /**
     * Calculation handler for mortgage calculator section
     */
    mortgageCalculation: function mortgageCalculation() {
      var homePrice = util.parseNumberInput('#homePrice');
      var downPaymentPercentage = util.parseNumberInput('#downPaymentPercentage');
      var annualInterestRate = util.parseNumberInput('#annualInterestRate');
      var loanTermInYears = util.parseNumberInput('#loanTermInYears');
      var annualPropertyTaxRate = util.parseNumberInput('#annualPropertyTaxRate');
      var annualHomeInsurance = util.parseNumberInput('#annualHomeInsurance');
      var monthlyHOAFees = util.parseNumberInput('#monthlyHOAFees');
      var pmi = util.parseNumberInput('#pmi');
      var downPayment = homePrice * (downPaymentPercentage / 100);
      var principal = homePrice - downPayment;
      var monthlyPayment = util.calculateMonthlyPayment(principal, annualInterestRate, loanTermInYears);
      var monthlyPropertyTax = homePrice * (annualPropertyTaxRate / 100) / 12;
      var monthlyHomeInsurance = annualHomeInsurance / 12;
      var pmiRequired = downPaymentPercentage < 20;
      var monthlyPMI = pmiRequired ? principal * (pmi / 100) / 12 : 0;
      var totalMonthlyPayment = monthlyPayment + monthlyPropertyTax + monthlyHomeInsurance + monthlyHOAFees + monthlyPMI;
      var loanAmount = homePrice - downPayment;

      // Format values
      var formattedDownPayment = util.numberFormat(downPayment, true);
      var formattedLoanAmount = util.numberFormat(loanAmount, true);
      var formattedMonthlyPayment = util.numberFormat(monthlyPayment, true);
      var formattedPropertyTax = util.numberFormat(monthlyPropertyTax, true);
      var formattedHomeInsurance = util.numberFormat(monthlyHomeInsurance, true);
      var formattedPMI = pmiRequired ? util.numberFormat(monthlyPMI, true) : '';
      var formattedHOAFees = util.numberFormat(monthlyHOAFees, true);
      var formattedTotalMonthlyPayment = util.numberFormat(totalMonthlyPayment, true);

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
      var chartData = [{
        label: 'Monthly Mortgage Payment',
        value: monthlyPayment,
        color: '#ff6384'
      }, {
        label: 'Property Tax',
        value: monthlyPropertyTax,
        color: '#36a2eb'
      }, {
        label: 'Home Insurance',
        value: monthlyHomeInsurance,
        color: '#ffce56'
      }];
      if (monthlyHOAFees > 0) {
        chartData.push({
          label: 'HOA',
          value: monthlyHOAFees,
          color: '#c2d500'
        });
      }
      if (pmiRequired && monthlyPMI > 0) {
        chartData.push({
          label: 'PMI',
          value: monthlyPMI,
          color: '#4bc0c0'
        });
      }
      this.updateChart(chartData);
    },
    /**
     * Update the mortgage donut chart
     * @param {Array} chartData - Chart data for visualization
     */
    updateChart: function updateChart(chartData) {
      var canvas = document.getElementById('mortgage-calculator-chart');
      if (!canvas) return;
      var ctx = canvas.getContext('2d');
      if (window.myChart) {
        window.myChart.destroy();
      }

      // Filter out zero values
      var filteredData = chartData.filter(function (item) {
        return item.value > 0;
      });
      window.myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: filteredData.map(function (item) {
            return item.label;
          }),
          datasets: [{
            data: filteredData.map(function (item) {
              return item.value;
            }),
            backgroundColor: filteredData.map(function (item) {
              return item.color;
            }),
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
                label: function label(context) {
                  var label = context.label || '';
                  var value = util.numberFormat(context.parsed, true);
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
    init: function init() {
      var self = this;

      // If the calculator form exists, set up event handlers
      if ($('#houzez-calculator-form').length > 0) {
        $('#houzez-calculator-form input').on('input', function () {
          self.mortgageCalculation();
        });

        // Calculate on page load
        setTimeout(function () {
          self.mortgageCalculation();
        }, 100);
      }
    }
  };

  // Initialize when document is ready
  $(document).ready(function () {
    MortgageCalculator.init();
  });

  // Also initialize on window load as fallback
  $(window).on('load', function () {
    if ($('#houzez-calculator-form').length > 0 && !window.myChart) {
      MortgageCalculator.init();
    }
  });
})(jQuery);
/******/ })()
;
//# sourceMappingURL=mortgage-calculator.js.map