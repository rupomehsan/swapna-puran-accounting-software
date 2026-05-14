<template>
 
    <Header />
    <div class="header-spacer"></div>
    <main class="main-wrapper">
      <slot />
    </main>
    <Footer />
  
</template>

<script>
import Header from "./Header.vue";
import Footer from "./Footer.vue";
export default {
  name: "Layout",
  components: { Header, Footer },
};
</script>

<style>
/* Global styles */
html, body {
  margin: 0; padding: 0;
  font-family: "Nunito", sans-serif;
  overflow-y: auto !important;
  overflow-x: hidden;
  height: auto !important;
  background: #080c18;
}

#app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  overflow: visible !important;
}

.header-spacer {
  height: 66px;
  flex-shrink: 0;
}

.main-wrapper {
  display: block;
  flex: 1;
  overflow: visible !important;
}

/* Custom color variables */
:root {
  --primary-color: #667eea;
  --secondary-color: #764ba2;
  --success-color: #28a745;
  --info-color: #17a2b8;
  --warning-color: #ffc107;
  --danger-color: #dc3545;
}

/* Override Bootstrap primary color */
.bg-primary {
  background: linear-gradient(
    135deg,
    var(--primary-color) 0%,
    var(--secondary-color) 100%
  ) !important;
}

.text-primary {
  color: var(--primary-color) !important;
}

.btn-primary {
  background: linear-gradient(
    135deg,
    var(--primary-color) 0%,
    var(--secondary-color) 100%
  );
  border: none;
}

.btn-primary:hover {
  background: linear-gradient(
    135deg,
    var(--secondary-color) 0%,
    var(--primary-color) 100%
  );
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Scrollbar styling */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: var(--primary-color);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: var(--secondary-color);
}
</style>
