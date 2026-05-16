<template>
  <Head title="Login" />
  <div class="login-root">
    <!-- Video Background (unchanged) -->
    <video class="bg-video" autoplay muted loop playsinline>
      <source src="/frontend/encryption-bg.webm" type="video/webm" />
    </video>
    <div class="bg-overlay"></div>

    <!-- Glass Effect Layer (unchanged) -->
    <div class="bg-glass" aria-hidden="true"></div>

    <!-- Binary Effect Canvas (unchanged) -->
    <canvas ref="rainCanvas" class="binary-canvas" aria-hidden="true"></canvas>

    <!-- Login Card -->
    <div class="login-card">
      <!-- Brand -->
      <div class="brand">
        <div class="brand__logo">
          <img v-if="siteLogo" :src="siteLogo" :alt="siteName" />
          <i v-else class="fas fa-handshake"></i>
        </div>
        <h1 class="brand__name">আমাদের স্বপ্ন পূরণ</h1>
        <p class="brand__tag">এডমিন  লগইন প্যানেল </p>
      </div>

      <form @submit.prevent="LoginSubmitHandler" class="login-form">
        <div class="form-row">
          <label class="form-label">
            <i class="fas fa-envelope"></i> Email Address
          </label>
          <input
            v-model="email"
            type="email"
            name="email"
            class="form-input"
            placeholder="user@example.com"
            autocomplete="email"
            required
          />
        </div>

        <div class="form-row">
          <label class="form-label">
            <i class="fas fa-lock"></i> Password
          </label>
          <div class="input-with-icon">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="password"
              name="password"
              class="form-input"
              placeholder="••••••••"
              autocomplete="current-password"
              required
            />
            <button
              type="button"
              class="eye-btn"
              @click="showPassword = !showPassword"
              :aria-label="showPassword ? 'Hide password' : 'Show password'"
            >
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
        </div>

        <div class="form-options">
          <label class="check-label">
            <input type="checkbox" v-model="rememberMe" />
            <span class="check-box"></span>
            <span class="check-text">Remember me</span>
          </label>
          <Link href="forgot-password" class="forgot-link">Forgot password?</Link>
        </div>

        <button type="submit" class="submit-btn" :disabled="loading">
          <span v-if="!loading">
            <i class="fas fa-right-to-bracket"></i> Sign In
          </span>
          <span v-else>
            <span class="spinner"></span> Signing in...
          </span>
        </button>
      </form>

      <div class="card-foot">
        <Link href="/" class="back-link">
          <i class="fas fa-arrow-left"></i> Back to home
        </Link>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { auth_store } from "../../GlobalStore/auth_store";
import { site_settings_store } from "../../GlobalStore/site_settings_store";

export default {
  layout: null,
  data() {
    return {
      loading: false,
      checkingAuth: true,
      showPassword: false,
      email: "superadmin@gmail.com",
      password: "12345678",
      rememberMe: false,
      _rainId: null,
    };
  },

  async created() {
    this.checkingAuth = true;
    const token = localStorage.getItem("auth_token");
    const role = localStorage.getItem("auth_role");

    if (token && role) {
      try {
        await this.check_is_auth();
        if (this.is_auth) {
          window.location.href = "/admin#/dashboard";
        } else {
          this.clearAuthData();
        }
      } catch {
        this.clearAuthData();
      }
    }
    this.checkingAuth = false;
  },

  mounted() {
    site_settings_store().get_all_website_settings();
    this.loadRememberedCredentials();
    this.startBinaryRain();
  },

  beforeUnmount() {
    if (this._rainId) cancelAnimationFrame(this._rainId);
  },

  computed: {
    ...mapState(auth_store, {
      auth_info: "auth_info",
      is_auth: "is_auth",
    }),
    siteLogo() {
      return site_settings_store().get_setting_value("image")
        || site_settings_store().get_setting_value("header_logo")
        || null;
    },
    siteName() {
      return site_settings_store().get_setting_value("site_name") || "আমাদের স্বপ্ন পূরণ";
    },
  },

  methods: {
    ...mapActions(auth_store, { check_is_auth: "check_is_auth" }),
    ...mapActions(site_settings_store, { get_setting_value: "get_setting_value" }),

    startBinaryRain() {
      const canvas = this.$refs.rainCanvas;
      if (!canvas) return;
      const ctx = canvas.getContext("2d");
      const fontSize = 18;
      let frame = 0;
      let mouseX = canvas.width / 2;
      let mouseY = canvas.height / 2;

      const resize = () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
      };
      resize();
      window.addEventListener("resize", resize);

      document.addEventListener("mousemove", (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
      });

      let rows = Math.ceil(canvas.height / fontSize);
      let cols = Math.ceil(canvas.width / fontSize);
      let binaryGrid = Array.from({ length: rows }, () =>
        Array.from({ length: cols }, () => (Math.random() > 0.5 ? "1" : "0")),
      );

      const draw = () => {
        frame++;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.font = `${fontSize}px "Courier New", monospace`;

        const hoverCol = Math.floor(mouseX / fontSize);
        const hoverRow = Math.floor(mouseY / fontSize);

        for (let row = 0; row < rows; row++) {
          for (let col = 0; col < cols; col++) {
            const bit = binaryGrid[row][col];
            const x = col * fontSize;
            const y = row * fontSize + fontSize;
            let opacity = 0.12 + 0.06 * Math.sin((row + col + frame * 0.02) * 0.1);
            if (row === hoverRow && col === hoverCol) opacity = 1;
            ctx.fillStyle = `rgba(0, 255, 210, ${opacity})`;
            ctx.fillText(bit, x, y);
          }
        }

        if (frame % 120 === 0) {
          const randRow = Math.floor(Math.random() * rows);
          const randCol = Math.floor(Math.random() * cols);
          binaryGrid[randRow][randCol] = Math.random() > 0.5 ? "1" : "0";
        }

        this._rainId = requestAnimationFrame(draw);
      };

      draw();
    },

    async LoginSubmitHandler() {
      try {
        this.loading = true;
        if (this.rememberMe) this.saveCredentials();
        else this.clearSavedCredentials();

        let formData = new FormData();
        formData.append("email", this.email);
        formData.append("password", this.password);
        formData.append("remember", this.rememberMe);

        let response = await axios.post("/login", formData);

        if (response.data?.status === "success") {
          let data = response.data?.data;
          if (data.access_token && data.user) {
            localStorage.setItem("auth_token", data.access_token);
            localStorage.setItem("auth_role", data.user.role_id);
            window.sessionStorage.removeItem("prevurl");
            window.s_alert("Login Successfully");
            let redirectUrl = "";
            if (data.user.role_id) redirectUrl = "/admin#/dashboard";
            if (redirectUrl) setTimeout(() => { window.location.href = redirectUrl; }, 100);
          } else {
            window.s_alert("Login failed. Invalid response from server.", "error");
          }
        } else {
          window.s_alert(response.data?.message || "Login failed. Please try again.", "error");
        }
      } catch (error) {
        if (error.response?.data?.message) window.s_alert(error.response.data.message, "error");
        else if (error.response?.status === 401) window.s_alert("Invalid credentials. Please check your email and password.", "error");
        else window.s_alert("Login failed. Please check your connection and try again.", "error");
      } finally {
        this.loading = false;
      }
    },

    saveCredentials() {
      localStorage.setItem("rememberedCredentials", JSON.stringify({
        email: this.email,
        password: this.password,
        rememberMe: this.rememberMe,
      }));
    },
    clearSavedCredentials() { localStorage.removeItem("rememberedCredentials"); },
    clearAuthData() {
      localStorage.removeItem("auth_token");
      localStorage.removeItem("auth_role");
      window.sessionStorage.removeItem("prevurl");
    },
    loadRememberedCredentials() {
      const saved = localStorage.getItem("rememberedCredentials");
      if (saved) {
        try {
          const c = JSON.parse(saved);
          this.email = c.email || "";
          this.password = c.password || "";
          this.rememberMe = c.rememberMe || false;
        } catch { this.clearSavedCredentials(); }
      }
    },
  },
};
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }

:global(body),
:global(html) {
  overflow: hidden !important;
  height: 100%;
}

.login-root {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: #020408;
  padding: 16px;
}

/* ─── Background (unchanged) ─── */
.bg-video {
  position: fixed;
  top: 50%; left: 50%;
  width: 100vw; height: 100vh;
  min-width: 100vw; min-height: 100vh;
  object-fit: cover;
  transform: translate(-50%, -50%) scale(1.6);
  z-index: 0;
  opacity: 1;
}
.bg-overlay {
  position: fixed; inset: 0;
  background: rgba(2, 4, 8, 0.08);
  z-index: 1;
}
.bg-glass {
  position: fixed; inset: 0; z-index: 2; pointer-events: none;
  backdrop-filter: blur(6px) saturate(140%);
  -webkit-backdrop-filter: blur(6px) saturate(140%);
  background: linear-gradient(
    135deg,
    rgba(2, 8, 14, 0.25) 0%,
    rgba(0, 255, 210, 0.03) 50%,
    rgba(2, 8, 14, 0.3) 100%
  );
  border-top: 1px solid rgba(255, 255, 255, 0.04);
  box-shadow: inset 0 0 120px rgba(0, 0, 0, 0.35);
}
.binary-canvas {
  position: fixed; inset: 0; z-index: 3; pointer-events: none;
  width: 100%; height: 100%;
}

/* ─── Login Card (professional) ─── */
.login-card {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 420px;
  max-height: calc(100vh - 32px);
  display: flex;
  flex-direction: column;
  background: rgba(13, 19, 32, 0.92);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 32px 30px 24px;
  backdrop-filter: blur(14px);
  box-shadow:
    0 20px 50px rgba(0, 0, 0, 0.5),
    0 0 0 1px rgba(99, 102, 241, 0.05);
  overflow-y: auto;
  animation: cardIn 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
}
@keyframes cardIn {
  from { opacity: 0; transform: translateY(20px) scale(0.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}
.login-card::-webkit-scrollbar { width: 4px; }
.login-card::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.08);
  border-radius: 4px;
}

/* Brand */
.brand { text-align: center; margin-bottom: 26px; }
.brand__logo {
  width: 66px; height: 66px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.04);
  border: 2px solid rgba(99, 102, 241, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.22);
}
.brand__logo img { width: 100%; height: 100%; object-fit: cover; }
.brand__logo i { font-size: 26px; color: #a5b4fc; }
.brand__name {
  font-size: 19px;
  font-weight: 800;
  color: #e2e8f0;
  margin: 0 0 4px;
  letter-spacing: 0.2px;
}
.brand__tag {
  font-size: 12.5px;
  color: #94a3b8;
  margin: 0;
}

/* Form */
.login-form { display: flex; flex-direction: column; gap: 14px; }
.form-row { display: flex; flex-direction: column; gap: 6px; }
.form-label {
  font-size: 12px;
  font-weight: 600;
  color: #94a3b8;
  letter-spacing: 0.2px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.form-label i { color: #64748b; font-size: 11px; }

.form-input {
  width: 100%;
  padding: 11px 14px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #e2e8f0;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
}
.form-input::placeholder { color: #475569; }
.form-input:hover { border-color: rgba(255, 255, 255, 0.16); }
.form-input:focus {
  border-color: rgba(99, 102, 241, 0.6);
  background: rgba(99, 102, 241, 0.06);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}

.input-with-icon { position: relative; }
.input-with-icon .form-input { padding-right: 42px; }
.eye-btn {
  position: absolute;
  right: 4px; top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  width: 32px; height: 32px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 8px;
  transition: color 0.2s, background 0.2s;
}
.eye-btn:hover { color: #a5b4fc; background: rgba(255, 255, 255, 0.04); }

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 4px;
}
.check-label {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  user-select: none;
}
.check-label input { display: none; }
.check-box {
  width: 15px; height: 15px;
  border: 1.5px solid rgba(255, 255, 255, 0.22);
  border-radius: 4px;
  position: relative;
  transition: all 0.2s;
}
.check-label input:checked + .check-box {
  background: #6366f1;
  border-color: #6366f1;
}
.check-label input:checked + .check-box::after {
  content: "";
  position: absolute;
  left: 4px; top: 1px;
  width: 4px; height: 8px;
  border: solid #fff;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}
.check-text { font-size: 12.5px; color: #94a3b8; }
.forgot-link {
  font-size: 12.5px;
  color: #a5b4fc;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}
.forgot-link:hover { color: #c4b5fd; text-decoration: underline; }

.submit-btn {
  width: 100%;
  padding: 12px 24px;
  margin-top: 8px;
  border: none;
  border-radius: 10px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 0.3px;
  cursor: pointer;
  transition: transform 0.15s, box-shadow 0.2s, opacity 0.2s;
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.32);
}
.submit-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 12px 32px rgba(99, 102, 241, 0.42);
}
.submit-btn:active:not(:disabled) { transform: translateY(0); }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.submit-btn i { margin-right: 5px; }

.spinner {
  display: inline-block;
  width: 14px; height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  margin-right: 6px;
  vertical-align: middle;
}
@keyframes spin { to { transform: rotate(360deg); } }

.card-foot {
  text-align: center;
  margin-top: 18px;
  padding-top: 14px;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
}
.back-link {
  font-size: 12.5px;
  color: #64748b;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: color 0.2s;
}
.back-link:hover { color: #a5b4fc; }
.back-link i { font-size: 10px; }

@media (max-width: 480px) {
  .login-card { padding: 24px 20px 18px; }
  .brand__logo { width: 56px; height: 56px; }
  .brand__name { font-size: 17px; }
}
</style>
