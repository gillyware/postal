# Release Notes for 1.x

## v1.1.2 2025-07-23
- Remove `main` branch by [@braxey](https://github.com/braxey) in TBD


## v1.1.1 – 2025‑07‑21
- Update banner and github/packagist links in README.

## v1.1.0 – 2025‑07‑21
- Add `explicitRules()` hook for runtime validation.
- Implement automatic merge of explicit and attribute rules (alias‑aware).
- Add new docs & unit tests covering the above.

## v1.0.0 – 2025‑07‑21
- Add attribute‑based validation via `#[Rule]`.
- Allow `#[Field]` input‑aliasing.
- Implement controller auto‑injection of packets.
- Add `Packetable` trait & interface.
- Add `prepareForValidation()` hook to mutate input before validation.
- Add `failedValidation()` hook to customise failure behaviour.
