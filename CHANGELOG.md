# Changelog
All notable changes to **Postal** are documented in this file.

## [1.1.1] – 2024‑07‑21
### Updated
- Banner and github/packagist links in README.

## [1.1.0] – 2024‑07‑21
### Added
- `explicitRules()` hook for runtime validation.
- Automatic merge of explicit and attribute rules (alias‑aware).
- New docs & unit tests covering the above.

## [1.0.0] – 2024‑07‑21
### Added
- Attribute‑based validation via `#[Rule]`.
- `#[Field]` input‑aliasing.
- Controller auto‑injection of packets.
- `Packetable` trait & interface.
- `prepareForValidation()` hook to mutate input before validation.
- `failedValidation()` hook to customise failure behaviour.
