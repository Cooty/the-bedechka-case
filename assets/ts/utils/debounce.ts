const debounce = (fn:Function, time:number) => {
    let timeout:number;

    return function() {
        const functionCall = function() { fn.apply(this, arguments) };

        window.clearTimeout(timeout);
        timeout = window.setTimeout(functionCall, time);
    }
};

export default debounce;