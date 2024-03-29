(function() {
    jQuery(document).ready(function(a) {
        "use strict";
        return window.Woodev_Payment_Form_Handler = function() {
            function b(b) {
                if (this.id = b.id, this.id_dasherized = b.id_dasherized, this.plugin_id = b.plugin_id, this.type = b.type, this.csc_required = b.csc_required, a("form.checkout").length) this.form = a("form.checkout"), this.handle_checkout_page();
                else if (a("form#order_review").length) this.form = a("form#order_review"), this.handle_pay_page();
                else {
                    if (!a("form#add_payment_method").length) return void console.log("No payment form found!");
                    this.form = a("form#add_payment_method"), this.handle_add_payment_method_page()
                }
                this.params = window[this.plugin_id + "_params"], "echeck" === this.type && this.form.on("click", ".js-woodev-payment-gateway-echeck-form-check-hint, .js-woodev-payment-gateway-echeck-form-sample-check", function(a) {
                    return function() {
                        return a.handle_sample_check_hint()
                    }
                }(this)), a(document).trigger("woodev_wc_payment_form_handler_init", {
                    id: this.id,
                    instance: this
                })
            }
            return b.prototype.handle_checkout_page = function() {
                return "credit-card" === this.type && a(document.body).on("updated_checkout", function(a) {
                    return function() {
                        return a.format_credit_card_inputs()
                    }
                }(this)), a(document.body).on("updated_checkout", function(a) {
                    return function() {
                        return a.set_payment_fields()
                    }
                }(this)), a(document.body).on("updated_checkout", function(a) {
                    return function() {
                        return a.handle_saved_payment_methods()
                    }
                }(this)), this.form.on("checkout_place_order_" + this.id, function(a) {
                    return function() {
                        return a.validate_payment_data()
                    }
                }(this))
            }, b.prototype.handle_pay_page = function() {
                return this.set_payment_fields(), "credit-card" === this.type && this.format_credit_card_inputs(), this.handle_saved_payment_methods(), this.form.submit(function(b) {
                    return function() {
                        return a("#order_review input[name=payment_method]:checked").val() === b.id ? b.validate_payment_data() : void 0
                    }
                }(this))
            }, b.prototype.handle_add_payment_method_page = function() {
                return this.set_payment_fields(), "credit-card" === this.type && this.format_credit_card_inputs(), this.form.submit(function(b) {
                    return function() {
                        return a("#add_payment_method input[name=payment_method]:checked").val() === b.id ? b.validate_payment_data() : void 0
                    }
                }(this))
            }, b.prototype.set_payment_fields = function() {
                return this.payment_fields = a(".payment_method_" + this.id)
            }, b.prototype.validate_payment_data = function() {
                var a;
                return this.form.is(".processing") ? !1 : (a = this.payment_fields.find(".js-wc-payment-gateway-payment-token:checked").val(), a ? !0 : "credit-card" === this.type ? this.validate_card_data() : this.validate_account_data())
            }, b.prototype.format_credit_card_inputs = function() {
                return a(".js-woodev-payment-gateway-credit-card-form-account-number").payment("formatCardNumber").change(), a(".js-woodev-payment-gateway-credit-card-form-expiry").payment("formatCardExpiry").change(), a(".js-woodev-payment-gateway-credit-card-form-csc").payment("formatCardCVC").change(), a(".js-woodev-payment-gateway-credit-card-form-input").on("change paste keyup", function(a) {
                    return function() {
                        return a.do_inline_credit_card_validation()
                    }
                }(this))
            }, b.prototype.do_inline_credit_card_validation = function() {
                var b, c;
                return c = a(".js-woodev-payment-gateway-credit-card-form-expiry"), b = a(".js-woodev-payment-gateway-credit-card-form-csc"), a.payment.validateCardExpiry(c.payment("cardExpiryVal")) ? c.addClass("identified") : c.removeClass("identified"), a.payment.validateCardCVC(b.val()) ? b.addClass("identified") : b.removeClass("identified")
            }, b.prototype.validate_card_data = function() {
                var b, c, d, e;
                return d = [], b = this.payment_fields.find(".js-woodev-payment-gateway-credit-card-form-account-number").val(), e = a.payment.cardExpiryVal(this.payment_fields.find(".js-woodev-payment-gateway-credit-card-form-expiry").val()), c = this.payment_fields.find(".js-woodev-payment-gateway-credit-card-form-csc").val(), b = b.replace(/-|\s/g, ""), b ? ((b.length < 12 || b.length > 19) && d.push(this.params.card_number_length_invalid), /\D/.test(b) && d.push(this.params.card_number_digits_invalid), a.payment.validateCardNumber(b) || d.push(this.params.card_number_invalid)) : d.push(this.params.card_number_missing), a.payment.validateCardExpiry(e) || d.push(this.params.card_exp_date_invalid), null != c && (c ? (/\D/.test(c) && d.push(this.params.cvv_digits_invalid), (c.length < 3 || c.length > 4) && d.push(this.params.cvv_length_invalid)) : d.push(this.params.cvv_missing)), d.length > 0 ? (this.render_errors(d), !1) : (this.payment_fields.find(".js-woodev-payment-gateway-credit-card-form-account-number").val(b), !0)
            }, b.prototype.validate_account_data = function() {
                var a, b, c;
                return b = [], c = this.payment_fields.find(".js-woodev-payment-gateway-echeck-form-routing-number").val(), a = this.payment_fields.find(".js-woodev-payment-gateway-echeck-form-account-number").val(), c ? (9 !== c.length && b.push(this.params.routing_number_length_invalid), /\D/.test(c) && b.push(this.params.routing_number_digits_invalid)) : b.push(this.params.routing_number_missing), a ? ((a.length < 3 || a.length > 17) && b.push(this.params.account_number_length_invalid), /\D/.test(a) && b.push(this.params.account_number_invalid)) : b.push(this.params.account_number_missing), b.length > 0 ? (this.render_errors(b), !1) : (this.payment_fields.find(".js-woodev-payment-gateway-echeck-form-account-number").val(a), !0)
            }, b.prototype.render_errors = function(b) {
                return a(".woocommerce-error, .woocommerce-message").remove(), this.form.prepend('<ul class="woocommerce-error"><li>' + b.join("</li><li>") + "</li></ul>"), this.form.removeClass("processing").unblock(), this.form.find(".input-text, select").blur(), a("html, body").animate({
                    scrollTop: this.form.offset().top - 100
                }, 1e3)
            }, b.prototype.handle_saved_payment_methods = function() {
                var b, c, d, e;
                return e = this.id_dasherized, d = this.csc_required, c = a("div.js-wc-" + e + "-new-payment-method-form"), b = c.find(".js-woodev-payment-gateway-credit-card-form-csc").parent(), a("input.js-wc-" + this.id_dasherized + "-payment-token").change(function() {
                    var f;
                    if (f = a("input.js-wc-" + e + "-payment-token:checked").val()) {
                        if (c.slideUp(200), d) return b.removeClass("form-row-last").addClass("form-row-first"), c.after(b)
                    } else if (c.slideDown(200), d) return b.removeClass("form-row-first").addClass("form-row-last"), c.find(".js-woodev-payment-gateway-credit-card-form-expiry").parent().after(b)
                }).change(), a("input#createaccount").change(function() {
                    var b;
                    return b = a("input.js-wc-" + e + "-tokenize-payment-method").closest("p.form-row"), a(this).is(":checked") ? (b.slideDown(), b.next().show()) : (b.hide(), b.next().hide())
                }).change()
            }, b.prototype.handle_sample_check_hint = function() {
                var a;
                return a = this.payment_fields.find(".js-woodev-payment-gateway-echeck-form-sample-check"), a.is(":visible") ? a.slideUp() : a.slideDown()
            }, b
        }()
    })
}).call(this);