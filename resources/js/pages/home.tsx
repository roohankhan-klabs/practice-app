import { Link } from "@inertiajs/react";
import { useEffect, useState } from "react";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

export default function Home() {
    const [loginEmail, setLoginEmail] = useState("");
    const [loginPassword, setLoginPassword] = useState("");
    const [registerName, setRegisterName] = useState("");
    const [registerEmail, setRegisterEmail] = useState("");
    const [registerPassword, setRegisterPassword] = useState("");
    const [registerConfirmPassword, setRegisterConfirmPassword] = useState("");
    const [loading, setLoading] = useState<"login" | "register" | null>(null);

    useEffect(() => {
        if (localStorage.getItem("token")) {
            window.location.href = "/welcome";
        }
    }, []);

    async function handleLogin(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();
        setLoading("login");

        try {
            const response = await fetch(`${API_BASE_URL}/login`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    email: loginEmail,
                    password: loginPassword,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                console.error("Login failed:", data);

                return;
            }

            console.log("Login success:", data);
            localStorage.setItem("token", data.data.token);
            window.location.href = "/welcome";
        } catch (error) {
            console.error("Login error:", error);
        } finally {
            setLoading(null);
        }
    }

    async function handleRegister(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();
        setLoading("register");

        try {
            const response = await fetch(`${API_BASE_URL}/register`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    name: registerName,
                    email: registerEmail,
                    password: registerPassword,
                    confirm_password: registerConfirmPassword,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                console.error("Register failed:", data);

                return;
            }

            console.log("Register success:", data);
            localStorage.setItem("token", data.data.token);
            window.location.href = "/welcome";
        } catch (error) {
            console.error("Register error:", error);
        } finally {
            setLoading(null);
        }
    }

    return (
        <>
            <div className="flex">
                <div className="p-3 flex border border-white">
                    <form className="flex flex-col gap-3" onSubmit={handleLogin}>
                        <input
                            type="email"
                            placeholder="Email"
                            name="email"
                            value={loginEmail}
                            onChange={(e) => setLoginEmail(e.target.value)}
                        />
                        <input
                            type="password"
                            placeholder="Password"
                            name="password"
                            value={loginPassword}
                            onChange={(e) => setLoginPassword(e.target.value)}
                        />
                        <button className="bg-white text-black px-2 py-1" type="submit" disabled={loading === "login"}>
                            {loading === "login" ? "Logging in..." : "Login"}
                        </button>
                        <div className="text-sm mt-2 underline">
                            <Link href="/forgot-password">Forgot Password</Link>
                        </div>
                    </form>
                </div>
                <div className="p-3 flex border border-white">
                    <form className="flex flex-col gap-3" onSubmit={handleRegister}>
                        <input
                            type="text"
                            placeholder="Name"
                            name="name"
                            value={registerName}
                            onChange={(e) => setRegisterName(e.target.value)}
                        />
                        <input
                            type="email"
                            placeholder="Email"
                            name="email"
                            value={registerEmail}
                            onChange={(e) => setRegisterEmail(e.target.value)}
                        />
                        <input
                            type="password"
                            placeholder="Password"
                            name="password"
                            value={registerPassword}
                            onChange={(e) => setRegisterPassword(e.target.value)}
                        />
                        <input
                            type="password"
                            placeholder="Confirm Password"
                            name="confirm_password"
                            value={registerConfirmPassword}
                            onChange={(e) => setRegisterConfirmPassword(e.target.value)}
                        />
                        <button className="bg-white text-black px-2 py-1" type="submit" disabled={loading === "register"}>
                            {loading === "register" ? "Registering..." : "Register"}
                        </button>
                    </form>
                </div>
            </div>
        </>
    );
}
