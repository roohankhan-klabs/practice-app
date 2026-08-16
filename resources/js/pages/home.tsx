import { Link } from "@inertiajs/react";
import { Mail, Lock, User, Store, CheckCircle2, ShoppingBag, Sparkles, ShieldCheck } from "lucide-react";
import { useEffect, useState } from "react";
import AlertError from "@/components/alert-error";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

export default function Home() {
    const [activeTab, setActiveTab] = useState<"login" | "register">("login");
    const [loginEmail, setLoginEmail] = useState("");
    const [loginPassword, setLoginPassword] = useState("");
    const [registerName, setRegisterName] = useState("");
    const [registerEmail, setRegisterEmail] = useState("");
    const [registerPassword, setRegisterPassword] = useState("");
    const [registerConfirmPassword, setRegisterConfirmPassword] = useState("");
    const [loading, setLoading] = useState<"login" | "register" | null>(null);
    const [errors, setErrors] = useState<string[]>([]);

    useEffect(() => {
        if (localStorage.getItem("token")) {
            window.location.href = "/welcome";
        }
    }, []);



    async function handleLogin(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();
        setLoading("login");
        setErrors([]);

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

                if (data.errors) {
                    setErrors(Object.values(data.errors).flat() as string[]);
                } else if (data.message) {
                    setErrors([data.message]);
                } else {
                    setErrors(["Invalid credentials. Please try again."]);
                }

                return;
            }

            console.log("Login success:", data);
            localStorage.setItem("token", data.data.token);
            window.location.href = "/welcome";
        } catch (error) {
            console.error("Login error:", error);
            setErrors(["Unable to connect. Please check your network connection."]);
        } finally {
            setLoading(null);
        }
    }

    async function handleRegister(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();
        setLoading("register");
        setErrors([]);

        if (registerPassword !== registerConfirmPassword) {
            setErrors(["Passwords do not match."]);
            setLoading(null);

            return;
        }

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

                if (data.errors) {
                    setErrors(Object.values(data.errors).flat() as string[]);
                } else if (data.message) {
                    setErrors([data.message]);
                } else {
                    setErrors(["Registration failed. Please check your inputs."]);
                }

                return;
            }

            console.log("Register success:", data);
            localStorage.setItem("token", data.data.token);
            window.location.href = "/welcome";
        } catch (error) {
            console.error("Register error:", error);
            setErrors(["Unable to connect. Please check your network connection."]);
        } finally {
            setLoading(null);
        }
    }

    return (
        <div className="flex min-h-screen w-full flex-col lg:flex-row bg-slate-50 dark:bg-slate-950 font-sans">
            {/* Left Column: Premium Branding & Info */}
            <div className="relative flex-1 hidden lg:flex flex-col justify-between p-12 bg-gradient-to-br from-indigo-900 via-indigo-950 to-slate-950 text-white overflow-hidden">
                {/* Decorative glowing background circles */}
                <div className="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-500/20 rounded-full blur-[120px]" />
                <div className="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-violet-600/10 rounded-full blur-[150px]" />

                {/* Header */}
                <div className="relative z-10 flex items-center gap-3">
                    <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 backdrop-blur-md border border-white/20">
                        <Store className="h-5 w-5 text-indigo-400" />
                    </div>
                    <span className="text-xl font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                        NexusMarket
                    </span>
                </div>

                {/* Main Content */}
                <div className="relative z-10 my-auto max-w-lg space-y-6">
                    <span className="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/10 px-3 py-1 text-sm font-medium text-indigo-300 ring-1 ring-inset ring-indigo-500/20">
                        <Sparkles className="h-3.5 w-3.5" /> Curated Marketplace Platform
                    </span>
                    <h2 className="text-4xl font-extrabold tracking-tight leading-tight lg:text-5xl">
                        Discover & support local independent shops.
                    </h2>
                    <p className="text-slate-300 text-lg leading-relaxed">
                        NexusMarket bridges the gap between passionate creators and curious shoppers. Explore catalogued items, custom variants, and unique offerings.
                    </p>

                    {/* Features list */}
                    <div className="space-y-4 pt-4">
                        <div className="flex items-start gap-3">
                            <CheckCircle2 className="h-5 w-5 text-emerald-400 mt-1 shrink-0" />
                            <div>
                                <h4 className="font-semibold text-slate-100">Multiple Curated Shops</h4>
                                <p className="text-sm text-slate-400">Browse product collections organized directly by individual shop owners.</p>
                            </div>
                        </div>
                        <div className="flex items-start gap-3">
                            <ShoppingBag className="h-5 w-5 text-emerald-400 mt-1 shrink-0" />
                            <div>
                                <h4 className="font-semibold text-slate-100">Tailored Variant Selection</h4>
                                <p className="text-sm text-slate-400">Order matching sizes, custom styles, and personalized options seamlessly.</p>
                            </div>
                        </div>
                        <div className="flex items-start gap-3">
                            <ShieldCheck className="h-5 w-5 text-emerald-400 mt-1 shrink-0" />
                            <div>
                                <h4 className="font-semibold text-slate-100">Secure Payments & Checkout</h4>
                                <p className="text-sm text-slate-400">Integrated safe transaction processes for complete peace of mind.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <div className="relative z-10 text-xs text-slate-500">
                    &copy; 2026 NexusMarket Inc. All rights reserved.
                </div>
            </div>

            {/* Right Column: Form Container */}
            <div className="flex flex-1 flex-col justify-center items-center p-6 sm:p-12 lg:p-24 relative">
                {/* Decorative bg on mobile */}
                <div className="absolute top-0 right-0 w-[30%] h-[30%] bg-indigo-500/10 rounded-full blur-[80px] lg:hidden -z-10" />

                <div className="w-full max-w-md space-y-6">
                    {/* Header on mobile */}
                    <div className="flex items-center gap-2.5 lg:hidden mb-6">
                        <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white">
                            <Store className="h-4 w-4" />
                        </div>
                        <span className="text-lg font-bold tracking-tight text-slate-900 dark:text-white">
                            NexusMarket
                        </span>
                    </div>

                    {/* Form switcher tabs */}
                    <div className="flex border-b border-slate-200 dark:border-slate-800 p-1 bg-slate-100 dark:bg-slate-900 rounded-lg">
                        <button
                            onClick={() => {
                                setActiveTab("login");
                                setErrors([]);
                            }}
                            className={`flex-1 py-2 text-sm font-semibold rounded-md transition-all ${activeTab === "login"
                                    ? "bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs"
                                    : "text-slate-500 hover:text-slate-900 dark:hover:text-white"
                                }`}
                        >
                            Sign In
                        </button>
                        <button
                            onClick={() => {
                                setActiveTab("register");
                                setErrors([]);
                            }}
                            className={`flex-1 py-2 text-sm font-semibold rounded-md transition-all ${activeTab === "register"
                                    ? "bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs"
                                    : "text-slate-500 hover:text-slate-900 dark:hover:text-white"
                                }`}
                        >
                            Sign Up
                        </button>
                    </div>

                    {/* Display Alerts */}
                    {errors.length > 0 && <AlertError errors={errors} title="Authentication Error" />}

                    {/* Forms */}
                    <Card className="border-slate-200 dark:border-slate-800 shadow-md">
                        <CardHeader className="space-y-1">
                            <CardTitle className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                                {activeTab === "login" ? "Welcome Back" : "Create Account"}
                            </CardTitle>
                            <CardDescription>
                                {activeTab === "login"
                                    ? "Enter your credentials to access your buyer dashboard"
                                    : "Fill in the details below to start your shopping journey"}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {activeTab === "login" ? (
                                <form onSubmit={handleLogin} className="space-y-4">
                                    <div className="grid gap-1.5">
                                        <Label htmlFor="login-email">Email Address</Label>
                                        <div className="relative">
                                            <Mail className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
                                            <Input
                                                id="login-email"
                                                type="email"
                                                placeholder="name@example.com"
                                                value={loginEmail}
                                                onChange={(e) => setLoginEmail(e.target.value)}
                                                className="pl-10"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div className="grid gap-1.5">
                                        <div className="flex items-center justify-between">
                                            <Label htmlFor="login-password">Password</Label>
                                            <Link
                                                href="/forgot-password"
                                                className="text-xs font-medium text-indigo-600 hover:underline dark:text-indigo-400"
                                            >
                                                Forgot Password?
                                            </Link>
                                        </div>
                                        <div className="relative">
                                            <Lock className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
                                            <Input
                                                id="login-password"
                                                type="password"
                                                placeholder="••••••••"
                                                value={loginPassword}
                                                onChange={(e) => setLoginPassword(e.target.value)}
                                                className="pl-10"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <Button
                                        type="submit"
                                        className="w-full bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-medium shadow-sm transition-colors py-2.5"
                                        disabled={loading === "login"}
                                    >
                                        {loading === "login" ? "Signing In..." : "Sign In"}
                                    </Button>
                                </form>
                            ) : (
                                <form onSubmit={handleRegister} className="space-y-4">
                                    <div className="grid gap-1.5">
                                        <Label htmlFor="reg-name">Full Name</Label>
                                        <div className="relative">
                                            <User className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
                                            <Input
                                                id="reg-name"
                                                type="text"
                                                placeholder="John Doe"
                                                value={registerName}
                                                onChange={(e) => setRegisterName(e.target.value)}
                                                className="pl-10"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div className="grid gap-1.5">
                                        <Label htmlFor="reg-email">Email Address</Label>
                                        <div className="relative">
                                            <Mail className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
                                            <Input
                                                id="reg-email"
                                                type="email"
                                                placeholder="name@example.com"
                                                value={registerEmail}
                                                onChange={(e) => setRegisterEmail(e.target.value)}
                                                className="pl-10"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div className="grid gap-1.5">
                                        <Label htmlFor="reg-password">Password</Label>
                                        <div className="relative">
                                            <Lock className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
                                            <Input
                                                id="reg-password"
                                                type="password"
                                                placeholder="••••••••"
                                                value={registerPassword}
                                                onChange={(e) => setRegisterPassword(e.target.value)}
                                                className="pl-10"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div className="grid gap-1.5">
                                        <Label htmlFor="reg-confirm">Confirm Password</Label>
                                        <div className="relative">
                                            <Lock className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
                                            <Input
                                                id="reg-confirm"
                                                type="password"
                                                placeholder="••••••••"
                                                value={registerConfirmPassword}
                                                onChange={(e) => setRegisterConfirmPassword(e.target.value)}
                                                className="pl-10"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <Button
                                        type="submit"
                                        className="w-full bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-medium shadow-sm transition-colors py-2.5"
                                        disabled={loading === "register"}
                                    >
                                        {loading === "register" ? "Creating Account..." : "Create Account"}
                                    </Button>
                                </form>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    );
}
