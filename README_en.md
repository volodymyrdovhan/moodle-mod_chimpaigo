# chimpAIgo! – Moodle activity plugin (`mod_chimpaigo`)

**Status:** Public launcher plugin. The actual AI quiz engine and all business logic run on the external platform at https://www.chimpaigo.com.

## What this plugin does
- Adds a *chimpAIgo!* activity to the Moodle activity chooser.
- Registers or uses an LTI 1.3 site-level tool pointing to the external chimpAIgo! platform.
- Keeps all application logic outside Moodle. This repository only contains the minimal Moodle integration code required to launch the external tool.

## Installation
1. Download the packaged ZIP from `dist/` (e.g., `dist/mod_chimpaigo_1_0_0.zip`).
2. In Moodle, go to *Site administration → Plugins → Install plugins* and upload the ZIP.
3. After installation, teachers can add *chimpAIgo!* from the activity chooser.

## LTI endpoints (reference)
- Launch: `https://www.chimpaigo.com/edu/moodle/lti/launch.aspx`
- OIDC initiate: `https://www.chimpaigo.com/edu/moodle/lti/oidc-init.aspx`
- JWKS: `https://www.chimpaigo.com/edu/moodle/lti/jwks.aspx`
- Access token: `https://www.chimpaigo.com/edu/moodle/lti/token.aspx`

> Note: Endpoints are hosted by the external chimpAIgo! service and may evolve. The plugin itself does not embed or disclose proprietary logic.

## Support and contact
- Maintainer: **Unbit Software S.L.**
- Email: **info@unbitsoftware.com**
- Please use this repository Issues for bug reports and questions.

## Licensing
The Moodle plugin code in this repository is released under **GNU GPL v3 or later**. The external chimpAIgo! service and its AI components are hosted outside Moodle and are not part of this distribution.
