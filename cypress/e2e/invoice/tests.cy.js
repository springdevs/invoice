/// <reference types="cypress" />

import { slowCypressDown } from 'cypress-slow-down';

slowCypressDown()

describe('Testing invoice plugin', () => {

    it("Tests", () => {
        cy.visit('/wp-login.php')
        cy.get("#user_login").type("admin");
        cy.get("#user_pass").type("password");
        cy.get("#loginform").submit();

        cy.visit('/shop')
        cy.get('[data-product_id="24"]').click({ multiple: true, force: true });
        cy.get('[data-product_id="16"]').click({ multiple: true, force: true });
        cy.get('[data-product_id="33"]').click({ multiple: true, force: true });
        cy.get('[data-product_id="17"]').click({ multiple: true, force: true });
        cy.get('[data-product_id="18"]').click({ multiple: true, force: true });

        cy.visit('/checkout')
        cy.get("form.woocommerce-checkout").submit();

        cy.visit('/my-account/orders/')
        cy.get(".pips_invoice:first").click({ multiple: true, force: true });
    })
})