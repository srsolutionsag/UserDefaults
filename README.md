ILIAS UserDefaults Plugin
=========================
This EventHook-Plugin allows to auto-generate Portfolios, Skills and Course-/Group-Memberships after creating an new account, based on the choices of UserDefiniedFields.

# Installation
Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Services/EventHandling/EventHook/
cd Customizing/global/plugins/Services/EventHandling/EventHook/
git clone https://github.com/fluxapps/UserDefaults.git UserDefaults
```
As ILIAS administrator go to "Administration->Plugins" and install/activate the plugin.

## ILIAS 7 core ilCtrl patch
For make this plugin work with ilCtrl in ILIAS 7, you may need to patch the core, before you update the plugin (At your own risk)

Start at the plugin directory

./vendor/srag/dic/bin/ilias7_core_apply_ilctrl_patch.sh

## Maintenance
fluxlabs ag, support@fluxlabs.ch

This project is maintained by fluxlabs. 
