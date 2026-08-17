declare global {
    interface Window {
        Flex: new (
            captureContext: string
        ) => CybersourceFlex;
    }
}

export interface CybersourceFlex {
    microform(options?: {
        styles?: Record<string, unknown>;
    }): CybersourceMicroform;
}

export interface CybersourceMicroform {
    createField(
        type: 'number' | 'securityCode',
        options?: Record<string, unknown>
    ): CybersourceField;

    createField(
        type: string,
        options?: Record<string, unknown>
    ): CybersourceField;

    createToken(
        options: Record<string, unknown>,
        callback: (
            error: unknown,
            response: CybersourceTokenResponse
        ) => void
    ): void;
}

export interface CybersourceField {
    load(
        selector: string
    ): void;

    unload(): void;

    clear(): void;

    on(
        event: string,
        callback: (...args: any[]) => void
    ): void;
}

export interface CybersourceTokenResponse {
    token?: string;

    data?: {
        keyId?: string;
        transientToken?: string;
        [key: string]: unknown;
    };

    [key: string]: unknown;
}

export {};
