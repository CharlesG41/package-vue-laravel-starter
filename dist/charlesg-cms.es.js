import { ref as f, openBlock as v, createElementBlock as x, createElementVNode as i, toDisplayString as m, unref as d, createApp as h } from "vue";
const p = f({}), g = f("");
function L() {
  const l = function(n, e = {}) {
    let t = n.split(".").reduce((o, y) => o == null ? void 0 : o[y], p.value);
    return typeof t == "string" && Object.keys(e).forEach((o) => {
      t = t.replace(`:${o}`, e[o]);
    }), t || n;
  }, r = (n) => {
    localStorage.setItem("userLanguage", n);
  }, a = () => localStorage.getItem("userLanguage") || document.documentElement.lang || "en", c = async function(n) {
    try {
      const t = await (await fetch("/change-language", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        },
        body: JSON.stringify({ locale: n })
      })).json();
      p.value = t.translations, g.value = t.locale, r(t.locale);
    } catch (e) {
      console.error("Failed to change language:", e);
    }
  };
  return {
    trans: l,
    setLocale: c,
    getLocale: function() {
      return g.value;
    },
    initTranslations: async function(n, e) {
      const t = a();
      t !== e ? await c(t) : (p.value = n, g.value = e);
    },
    getLanguagePreference: a,
    currentLocale: g
  };
}
const T = {
  __name: "ExampleComponent",
  setup(l) {
    const { trans: r, setLocale: a, getLocale: c, currentLocale: u } = L(), s = async (n) => {
      console.log("changeLanguage", n), await a(n);
    };
    return (n, e) => (v(), x("div", null, [
      i("button", {
        onClick: e[0] || (e[0] = (t) => s("en"))
      }, "English"),
      i("button", {
        class: "bg-gray-500 text-white px-4 py-2 rounded-md",
        onClick: e[1] || (e[1] = (t) => s("fr"))
      }, "Francasdasdadis tbnk"),
      i("p", null, "Current language: " + m(d(u)), 1),
      i("p", null, "Translated text: " + m(d(r)("cyvian.static.add")), 1)
    ]));
  }
};
function E(l, r) {
  const a = h({
    setup() {
      const { initTranslations: c, getLanguagePreference: u, setLocale: s, trans: n } = L(), e = u();
      return c(r, e), {
        setLocale: s,
        trans: n
      };
    },
    template: "<example-component></example-component>"
  });
  a.component("example-component", T), a.mount(l);
}
export {
  E as init
};
