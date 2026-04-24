import React from 'react';

export default function FormStep({ list = [], active = 0 }) {
  return (
    <div className="d-flex align-items-center gap-4 mb-4 form-step-item">

      {list.map((item, index) => (
        <React.Fragment key={index}>
          
          <Step
            number={index + 1}
            label={item}
            active={index === active}
          />

          {/* Divider trừ phần tử cuối */}
          {index < list.length - 1 && <Divider />}
        </React.Fragment>
      ))}

    </div>
  );
}

function Step({ number, label, active }) {
  return (
    <div className={`d-flex align-items-center gap-2 ${active ? "" : "opacity-50"}`}>
      <div
        className={`rounded-circle d-flex align-items-center justify-content-center 
          ${active ? "bg-primary text-white" : "bg-secondary text-light"}`}
        style={{ width: 36, height: 36 }}
      >
        {number}
      </div>
      <span className={active ? "theme-title-highlight" : "theme-title"}>{label}</span>
    </div>
  );
}

function Divider() {
  return <div className="flex-grow-1" style={{ height: 1, background: "#444" }} />;
}
