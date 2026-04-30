import React, { useEffect } from "react";
import { useI18n } from "../../i18n/useI18n";
import { useSearchParams } from "react-router-dom";

export default function LanguageSwitcher() {
  const [searchParams] = useSearchParams();
  const { lang, setLang } = useI18n();
  useEffect(() => {
    if(searchParams.get('lang')) {
      setLang(searchParams.get('lang'))
    }
  },[])
  return (
    <div className="d-flex align-items-center gap-2">
      <i className="bi bi-translate fs-5 text-muted"></i>

      <select
        className="form-select form-select-sm"
        value={lang}
        onChange={(e) => setLang(e.target.value)}
        style={{ width: 140 }}
      >
        <option value="en">🇺🇸 English</option>
        <option value="vi">🇻🇳 Tiếng Việt</option>
        <option value="ja">🇯🇵 日本語</option>
      </select>
    </div>
  );
}
