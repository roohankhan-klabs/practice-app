let flexScriptPromise: Promise<void> | null = null;

function resolveFlexScriptSource(
    captureContextJwt?: string
) {
    if (captureContextJwt) {
        const parts = captureContextJwt.split('.');

        if (parts.length >= 2) {
            try {
                const payload = JSON.parse(
                    atob(
                        parts[1]
                            .replace(/-/g, '+')
                            .replace(/_/g, '/')
                    )
                ) as {
                    ctx?: Array<{
                        data?: {
                            clientLibrary?: string;
                        };
                    }>;
                };

                const clientLibrary =
                    payload.ctx?.[0]?.data?.clientLibrary;

                if (
                    typeof clientLibrary === 'string' &&
                    clientLibrary !== ''
                ) {
                    return clientLibrary;
                }
            } catch {
                // Fall through to the default library path.
            }
        }
    }

    return 'https://testflex.cybersource.com/microform/bundle/v2/flex-microform.min.js';
}

export function loadFlexScript(
    captureContextJwt?: string
): Promise<void> {
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

        script.src = resolveFlexScriptSource(
            captureContextJwt
        );

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
