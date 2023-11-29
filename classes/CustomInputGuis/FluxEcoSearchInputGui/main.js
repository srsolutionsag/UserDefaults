import {FluxEcoUiInputs} from "../lib/flux-eco-ui-input/FluxEcoUiInputs.mjs";
import {
    FluxEcoUIInputStyleSheets
} from "../lib/flux-eco-ui-element-style/styles/input/FluxEcoUIInputStyleSheets.mjs";


console.log(fluxEcoUiSearchInputConf);

var formContainer = document.getElementById(["il_prop_cont",fluxEcoUiSearchInputConf.name].join("_")).getElementsByTagName('div')[0]


FluxEcoUiInputs.create({styleSheets: FluxEcoUIInputStyleSheets.create()}).createSearch({
    name: fluxEcoUiSearchInputConf.name,
    dataSrc: fluxEcoUiSearchInputConf.dataSrc
}).render(formContainer);