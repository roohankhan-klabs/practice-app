let flexScriptPromise: Promise<void> | null = null;

export function loadFlexScript(): Promise<void> {
    if (flexScriptPromise) {
        return flexScriptPromise;
    }

    flexScriptPromise = new Promise((resolve, reject) => {
        const existingScript = document.querySelector(
            'script[data-cybersource-flex]'
        );

        if (existingScript) {
            resolve();
            
            return;
        }

        const script = document.createElement('script');

        script.src =
            'https://testflex.cybersource.com/microform/bundle/v2.0.2/flex-microform.min.js';

        script.async = true;

        script.dataset.cybersourceFlex = 'true';

        script.onload = () => resolve();

        script.onerror = () => {
            flexScriptPromise = null;

            reject(
                new Error(
                    'Failed to load Cybersource Flex Microform.'
                )
            );
        };

        document.head.appendChild(script);
    });

    return flexScriptPromise;
}
