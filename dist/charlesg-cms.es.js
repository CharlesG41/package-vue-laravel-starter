import { ref as f, openBlock as b, createElementBlock as y, createElementVNode as u, toDisplayString as m, unref as i, createApp as L } from "vue";
function S() {
  const g = window.__INITIAL_TRANSLATIONS__, a = f(g), s = function(o, n = {}) {
    let e = o.split(".").reduce((r, _) => r == null ? void 0 : r[_], a.value);
    return typeof e == "string" && Object.keys(n).forEach((r) => {
      e = e.replace(`:${r}`, n[r]);
    }), e || o;
  }, d = (o) => {
    localStorage.setItem("userLanguage", o);
  }, c = () => localStorage.getItem("userLanguage") || document.documentElement.lang || "en", t = f(c()), l = async function(o) {
    try {
      const e = await (await fetch("/change-language", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        },
        body: JSON.stringify({ locale: o })
      })).json();
      a.value = e.translations, t.value = e.locale, d(e.locale);
    } catch (n) {
      console.error("Failed to change language:", n);
    }
  }, v = function() {
    return t.value;
  }, p = async function(o, n) {
    const e = c();
    console.log(e, n), e !== n ? await l(e) : (a.value = o, t.value = n);
  };
  return p(a.value, t.value), {
    trans: s,
    setLocale: l,
    getLocale: v,
    initTranslations: p,
    getLanguagePreference: c,
    currentLocale: t
  };
}
const T = { class: "space-y-8" }, N = { class: "bg-yellow-500" }, h = {
  __name: "App",
  setup(g) {
    const { trans: a, setLocale: s, currentLocale: d } = S();
    return (c, t) => (b(), y("div", T, [
      u("div", N, m(i(a)("cyvian.static.add")), 1),
      u("button", {
        class: "mx-2 p-2 border rounded border-blue-400",
        onClick: t[0] || (t[0] = (l) => i(s)("en"))
      }, "EN"),
      u("button", {
        class: "mx-2 p-2 border rounded border-blue-400",
        onClick: t[1] || (t[1] = (l) => i(s)("fr"))
      }, "FR"),
      u("div", null, " Current locale is: " + m(i(d)), 1)
    ]));
  }
};
L(h).mount("#app");
