module.exports = {
  printWidth: 100,
  tabWidth: 4,
  useTabs: false,
  singleQuote: true,
  semi: true,
  trailingComma: "es5",
  endOfLine: "lf",
  phpVersion: "composer",
  plugins: ["@prettier/plugin-php"],
  overrides: [
    {
      files: "*.php",
      options: {
        parser: "php",
      },
    },
  ],
};
