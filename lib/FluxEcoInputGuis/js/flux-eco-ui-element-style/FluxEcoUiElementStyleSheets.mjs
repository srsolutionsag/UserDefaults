import FluxEcoUIButtonStyleSheets from "./styles/button/FluxEcoUIButtonStyleSheets.mjs";
import FluxEcoUIGridStyleSheets from "./styles/grid/FluxEcoUIGridStyleSheets.mjs";
import FluxEcoUIInputStyleSheets from "./styles/input/FluxEcoUIInputStyleSheets.mjs";

export default class FluxEcoUiElementStyleSheets {

    /**
     * @type {{fluxEcoUiButtonStyle: null|FluxEcoUIButtonStyleSheets}}
     * @type {{fluxEcoUiGridStyleSheets: null|FluxEcoUIGridStyleSheets}}
     * @type {{fluxEcoUiInputStyleSheets: null|FluxEcoUIInputStyleSheets}}
     */
    #state = {
        fluxEcoUiButtonStyle: null,
        fluxEcoUiGridStyleSheets: null,
        fluxEcoUiInputStyleSheets: null
    }

    constructor() {
    }

    /**
     * @return {FluxEcoUiElementStyleSheets}
     */
    static create() {
        const fluxEcoUiElementStyle = new this();
        return fluxEcoUiElementStyle;
    }

    /**
     * @return {FluxEcoUIButtonStyleSheets}
     */
    get buttonStyleSheets() {
        if(this.#state.fluxEcoUiButtonStyle === null) {
            this.#applyStateChanged({valueName: "fluxEcoUiButtonStyle", value: FluxEcoUIButtonStyleSheets.create()});
        }
        return this.#state.fluxEcoUiButtonStyle;
    }


    /**
     * @return {FluxEcoUIGridStyleSheets}
     */
    get gridStyleSheets() {
        if(this.#state.fluxEcoUiGridStyleSheets === null) {
            this.#applyStateChanged({valueName: "fluxEcoUiGridStyleSheets", value: FluxEcoUIGridStyleSheets.create()});
        }
        return this.#state.fluxEcoUiGridStyleSheets;
    }

    /**
     * @return {FluxEcoUIInputStyleSheets}
     */
    get inputStyleSheets() {
        if(this.#state.fluxEcoUiGridStyleSheets === null) {
            this.#applyStateChanged({valueName: "fluxEcoUiInputStyleSheets", value: FluxEcoUIInputStyleSheets.create()});
        }
        return this.#state.fluxEcoUiInputStyleSheets;
    }

    /**
     * @param {{valueName: string, value: any}} stateChanged
     */
    #applyStateChanged(stateChanged) {
        this.#state[stateChanged.valueName] = stateChanged.value
    }
}