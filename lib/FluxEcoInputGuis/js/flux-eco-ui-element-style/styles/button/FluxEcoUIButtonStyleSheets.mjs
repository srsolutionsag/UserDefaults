import baseButtonStyleSheet from './css/base-button.css' assert {type: 'css'};
import primaryButtonStyleSheet from './css/primary-button.css' assert {type: 'css'};

export default class FluxEcoUIButtonStyleSheets {

    /**
     * @type {{baseButtonStyleSheet: null|CSSStyleSheet, primaryButtonStyleSheet: null|CSSStyleSheet}}
     */
    #state = {
        baseButtonStyleSheet: null,
        primaryButtonStyleSheet: null
    }

    constructor() {
    }

    /**
     * @return {FluxEcoUIButtonStyleSheets}
     */
    static create() {
        const fluxEcoUIButtonStyle = new this();

        fluxEcoUIButtonStyle.#applyChangeStateValue({valueName: "baseButtonStyleSheet", value: baseButtonStyleSheet});
        fluxEcoUIButtonStyle.#applyChangeStateValue({
            valueName: "primaryButtonStyleSheet",
            value: primaryButtonStyleSheet
        });

        return fluxEcoUIButtonStyle;
    }

    get primaryButtonStyleSheet() {
        const styleSheet = this.#state.primaryButtonStyleSheet;
        for (const rule of this.#state.baseButtonStyleSheet.cssRules) {
            styleSheet?.insertRule(rule.cssText, 0);
        }
        return styleSheet;
    }


    /**
     * @param {{valueName: string, value: any}} params
     */
    #applyChangeStateValue(params) {
        this.#state[params.valueName] = params.value
    }
}