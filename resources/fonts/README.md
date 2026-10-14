# Fonts for wali pages

Inter keeps the original weights and Unicode coverage. Material Symbols Rounded
contains the fixed icons used by wali pages and every supported avatar preset.
Both retain their original family names and shapes.

These WOFF2 files are served by the application. Vite assigns content hashes to
their build URLs; normal builds do not contact Google Fonts.

To add a wali icon, update the sorted `wali-icons.json` list and run
`node scripts/update-wali-fonts.mjs`. Review the resulting manifest and fonts.
The manifest records source URLs, sizes, and SHA-256 checksums. Original licenses
are kept in `licenses/`.

Sources:
- [Inter on Google Fonts](https://github.com/google/fonts/tree/main/ofl/inter)
- [Material Symbols optimization and self-hosting](https://developers.google.com/fonts/docs/material_symbols)
