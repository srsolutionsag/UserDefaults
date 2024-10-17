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

## Rebuild & Maintenance

some changes by DHBW, test thoroughly, no function granted

original releases:

fluxlabs ag, support@fluxlabs.ch

This project needs to be rebuilt before it can be maintained.

Are you interested in a rebuild and would you like to participate?
Take advantage of the crowdfunding opportunity under [discussions](https://github.com/fluxapps/UserDefaults/discussions/7).

## About fluxlabs plugins

Please also have a look at our other key projects and their [MAINTENANCE](https://github.com/fluxapps/docs/blob/8ce4309b0ac64c039d29204c2d5b06723084c64b/assets/MAINTENANCE.png).

The plugins that require a rebuild and the costs are listed here: [REBUILDS](https://github.com/fluxapps/docs/blob/8ce4309b0ac64c039d29204c2d5b06723084c64b/assets/REBUILDS.png)

