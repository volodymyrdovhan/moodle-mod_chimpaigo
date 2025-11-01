# chimpAIgo! – Moodle activity plugin (`mod_chimpaigo`)

Repositorio público del módulo **mod_chimpaigo** para Moodle. Este repositorio expone el código del *plugin de actividad* que actúa como lanzador LTI 1.3 hacia la plataforma externa **chimpAIgo!**. Toda la lógica de IA y generación de cuestionarios reside en **www.chimpaigo.com**.

## Estructura
- `plugin/` → **código del plugin** extraído *tal cual* del paquete publicado. **No modificado**.
- `dist/mod_chimpaigo_1_0_0.zip` → artefacto de *release* listo para instalar en Moodle.
- `.github/` → plantillas de issues y flujo de CI mínimo.
- `CHANGELOG.md`, `LICENSE.txt`, `SECURITY.md`, `CONTRIBUTING.md`.

## Instalación en Moodle
1. Descarga `dist/mod_chimpaigo_1_0_0.zip`.
2. En Moodle: *Site administration → Plugins → Install plugins*.
3. Sube el ZIP.
4. Verifica que aparece la actividad **chimpAIgo!** en el selector.

## Endpoints LTI (referencia)
Este plugin registra/usa los endpoints LTI 1.3 de la plataforma externa:
- Launch: `https://www.chimpaigo.com/edu/moodle/lti/launch.aspx`
- OIDC initiate: `https://www.chimpaigo.com/edu/moodle/lti/oidc-init.aspx`
- JWKS: `https://www.chimpaigo.com/edu/moodle/lti/jwks.aspx`
- Access token: `https://www.chimpaigo.com/edu/moodle/lti/token.aspx`

> Nota: la lógica, UI y datos viven fuera de Moodle. Este plugin es un contenedor de lanzamiento.

## Soporte
- Incidencias y dudas: usa la pestaña **Issues** de este repositorio.
- Política de seguridad: ver `SECURITY.md`.

## Licencia
El plugin de Moodle se publica bajo **GPLv3 o posterior**. Ver `LICENSE.txt`.
