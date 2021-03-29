const isWebPSupported = ()=> {
    const img = new Image();

    return new Promise((resolve) => {
        img.onload = ()=> {
            resolve(!!(img.height > 0 && img.width > 0));
        };

        img.onerror = ()=> {
            resolve(false);
        };

        img.src = "data:image/webp;base64,UklGRjIAAABXRUJQVlA4ICYAAACyAgCdASoCAAEALmk0mk0iIiIiIgBoSygABc6zbAAA/v56QAAAAA==";
    });
};

export default isWebPSupported;