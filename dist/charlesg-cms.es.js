import { ref as d, openBlock as y, createElementBlock as L, createElementVNode as f, toDisplayString as m, unref as l, createTextVNode as S, createApp as b } from "vue";
function E() {
  const c = d({}), a = d(""), s = function(t, n = {}) {
    let e = t.split(".").reduce((r, v) => r == null ? void 0 : r[v], c.value);
    return typeof e == "string" && Object.keys(n).forEach((r) => {
      e = e.replace(`:${r}`, n[r]);
    }), e || t;
  }, u = (t) => {
    localStorage.setItem("userLanguage", t);
  }, i = () => localStorage.getItem("userLanguage") || document.documentElement.lang || "en", o = async function(t) {
    try {
      const e = await (await fetch("/change-language", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        },
        body: JSON.stringify({ locale: t })
      })).json();
      c.value = e.translations, a.value = e.locale, u(e.locale);
    } catch (n) {
      console.error("Failed to change language:", n);
    }
  }, g = function() {
    return a.value;
  }, p = async function(t, n) {
    const e = i();
    e !== n ? await o(e) : (c.value = t, a.value = n);
  };
  return p(c.value, a.value), {
    trans: s,
    setLocale: o,
    getLocale: g,
    initTranslations: p,
    getLanguagePreference: i,
    currentLocale: a
  };
}
const T = { class: "bg-green-500" }, _ = {
  __name: "App",
  setup(c) {
    const { trans: a, setLocale: s, currentLocale: u } = E();
    return (i, o) => (y(), L("div", null, [
      f("div", T, m(l(a)("cyvian.static.add")), 1),
      f("button", {
        onClick: o[0] || (o[0] = (g) => l(s)("en"))
      }, "EN"),
      f("button", {
        onClick: o[1] || (o[1] = (g) => l(s)("fr"))
      }, "FR"),
      S(" Current locale est: " + m(l(u)), 1)
    ]));
  }
};
b(_).mount("#app");
