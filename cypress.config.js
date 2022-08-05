const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    baseUrl: 'http://springdevs.local',
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
