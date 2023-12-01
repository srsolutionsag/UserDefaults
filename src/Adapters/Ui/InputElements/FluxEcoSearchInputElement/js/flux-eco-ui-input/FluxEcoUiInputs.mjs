import FluxEcoUiSearchInputElement from "./elements/FluxEcoUiSearchInputElement.mjs";

export default class FluxEcoUiInputs {

    /**
     * @typedef {{tagName: string, elements: FluxEcoUiInputElements, styleSheets: FluxEcoUiInputStyleSheets}} FluxEcoUiInputsState
     * @typedef {{searchInputStyleSheet}} FluxEcoUiInputStyleSheets
     * @typedef {Object <FluxEcoUiSearchInputElement>} FluxEcoUiInputElements
     * @type {FluxEcoUiInputsState}
     */
    #state = {
        tagName: 'flux-eco-ui-input-element',
        elements: {},
        styleSheets: {
            searchInputStyleSheet: new CSSStyleSheet()
        }
    }

    constructor() {
    }

    get #tageName() {
        return this.#state.tagName;
    }

    /**
     * @return {FluxEcoUiInputStyleSheets}
     */
    get #styleSheets() {
        return this.#state.styleSheets;
    }

    /**
     * @param name
     * @return {FluxEcoUiSearchInputElement}
     */
    get(name) {
        if (this.#has(name) === false) {
            console.error(["input", name, "not exists:"].join(" "));
        }
        return this.#state.elements[name];
    }

    /**
     * @param {string} name
     * @return boolean
     */
    #has(name) {
        return this.#state.elements.hasOwnProperty(name);
    }


    /**
     * @param {{styleSheets: FluxEcoUiInputStyleSheets}} params
     * @return {FluxEcoUiInputs}
     */
    static create(params) {
        const fluxEcoUiForms = new this();
        if(customElements.get(fluxEcoUiForms.#tageName) === undefined) {
            customElements.define(fluxEcoUiForms.#tageName, FluxEcoUiSearchInputElement);
        }


        return fluxEcoUiForms.#applyStateChanged({
            valueName: "styleSheets",
            value: params.styleSheets
        });
    }

    /**
     * @param {{name: string, selectedIds: array, dataSrc: string}} params
     * @return {FluxEcoUiSearchInputElement};
     */
    createSearch(params) {
        return this.#createSearchInputElement(Object.assign(params, {styleSheet: this.#styleSheets.searchInputStyleSheet}));
    }

    /**
     * @param {{name: string, selectedIds: array, dataSrc: string, styleSheet: CSSStyleSheet}} params
     * @return {FluxEcoUiSearchInputElement};
     */
    #createSearchInputElement(params) {
        if (this.#has(params.name)) {
            console.error(["input", params.name, "already exists:"].join(" "));
        }
        this.#applyStateChanged({
            valueName: "elements",
            value: Object.assign(this.#state.elements, {[params.name]: FluxEcoUiSearchInputElement.create(params)})
        });
        return this.get(params.name);
    }

    /**
     * @param {{valueName: string, value: any}} stateChanged
     * @return FluxEcoUiInputs
     */
    #applyStateChanged(stateChanged) {
        this.#state[stateChanged.valueName] = stateChanged.value;
        return this;
    }
}