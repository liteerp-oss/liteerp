import React, { useCallback, useEffect, useMemo, useRef, useState } from "react";
import Select from "react-select";
import { InputForm } from "./InputForm";

export default function SearchSelect({
  options = [],
  search = (text) => { },
  changeValue = (key, value) => { },
  value = null,
  disabled = false,
  errorMessage = null,
  defaultKeywords = '',
  name = '',
  placeholder = 'Search...',
  required = false,
  label = null
}) {
  const historyKeyword = useRef('');
  const keywords = useRef('');
  const [wait, setWait] = useState(false);
  const [localValue, setLocalValue] = useState(null);
  const [loading, setLoading] = useState(false);
  useEffect(() => {
    if (wait || historyKeyword.current === keywords.current || keywords.current === '') {
      return;
    }
    setLoading(true);
    search(keywords.current, () => {
      setLoading(false);
    });
    historyKeyword.current = keywords.current;

  }, [keywords.current, search, wait, historyKeyword]);
  useEffect(() => {
    if (options.length === 0 && defaultKeywords !== '') {
      keywords.current = defaultKeywords;
    }
    options.map((item) => {
      if (value === item.value) {
        setLocalValue(item);
      }
    })
  }, [options, keywords.current])
  /**
   * Get current value
   */
  useEffect(() => {
    setLoading(true);
    search(defaultKeywords, () => {
      setLoading(false);
    });
  }, [defaultKeywords]);
  return (
    disabled ? <div>
      {label ? <label>
        {label}
        {required ? <span className='text-danger'>*</span> : null}
      </label> : null}
      <InputForm disabled={true} value={localValue?.label} />
    </div> :
      <div className="erp-search-select">
        {label ? <label>
          {label}
          {required ? <span className='text-danger'>*</span> : null}
        </label> : null}
        <Select
          disabled={disabled}
          value={localValue}
          onInputChange={(text) => {
            if (keywords.current === text) {
              return;
            }
            keywords.current = text;
            if (wait) {
              return;
            }
            if (wait === false) {
              setWait(true);
            }
            setTimeout(() => {
              setWait(false);
            }, 500);
          }}
          onChange={(item) => {
            setLocalValue(item);
            changeValue(name, item.value)
          }}
          options={options}
          placeholder={placeholder}
          className={(errorMessage ? 'is-invalid' : '') + " search-select"}
        />
        {errorMessage ? <div className="invalid-feedback">
          {errorMessage.map((mess, index) => {
            return <p key={index}>{mess}</p>
          })}
        </div> : null}
        {loading ? <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div> : null}

      </div>
  );
}
