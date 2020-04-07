# TYPO3 Extension `insert_records_overlaymode`

Since TYPO3 Version 9.5 LTS an automatic overlay is done for records selected in the content element "Insert Records".
The benefit is that if this content element is translated, there is no need to change the selected record from default to the current language anymore.5

However, the drawback is that it is **not** possible anymore to render a record in the selected language.

## Installation

Install this extension just as any other extension. Either download it from TER or get it via composer with
```
composer require georgringer/insert-record-overlaymode
```

After the installation, include the TypoScript from the extension. Either select it in the sys_template record or copy
```
tt_content.shortcut.variables.shortcuts.overlayMode.field = overlay_mode
```
to your site package.

## Usage
A new checkbox has been added to the content element "Insert Records".
Editors can now choose if the language overlay should be done or not.

![Checkbox](/Resources/Public/Documentation/Screenshot.png)


## Resources

- Link to the forge issue describing the problem https://forge.typo3.org/issues/87982
