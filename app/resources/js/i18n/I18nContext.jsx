import React, { createContext, useEffect, useState } from "react";
import { i18nMessages } from "./locales/autoload";

export const I18nContext = createContext({
  lang: "en",
  t: (key) => key,
  setLang: () => { },
});

export const I18nProvider = ({ children }) => {
  const [lang, setLang] = useState(
    localStorage.getItem("lang") || "en"
  );

  useEffect(() => {
    localStorage.setItem("lang", lang);
  }, [lang]);

  const t = (key) => {
    return (
      key
        .split(".")
        .reduce((obj, k) => (obj ? obj[k] : null), i18nMessages[lang]) ||
      key
    );
  };

  return (
    <I18nContext.Provider value={{ lang, t, setLang }}>
      {children}
    </I18nContext.Provider>
  );
};
