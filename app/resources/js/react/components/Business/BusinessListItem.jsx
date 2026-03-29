import React from "react";
import SuccessButton from "../UI/Buttons/SuccessButton";
import FlatIcon32 from "../UI/FlatIcons/FlatIcon32";
import { useI18n } from "@/i18n/useI18n";
import StatusBadge from "../StatusBadge";

export default function BusinessListItem({
  business = null,
  onViewDetail = () => { },
  buttonBottomText = 'Dashboard',
  renderItem = null
}) {
  const { t } = useI18n();
  return (
    <div className="theme-card border rounded-4">
      <div className="row p-3">
        {/* Left Section */}
        <div className="membership-left col-8">
          <div>
            <div className="row">
              <div className="membership-icon">
                <FlatIcon32 size={64} name={"prenium64x64"} />
              </div>
            </div>
          </div>
        </div>

        {/* Right Section */}
        <div className="membership-right col-4">
          <SuccessButton width={200} label={buttonBottomText} onClick={onViewDetail} />
        </div>
      </div>
      <div className="container mb-3">

        <div className="membership-info">
          <h5 className="theme-title-highlight">{business?.name} </h5>
          <p className="theme-title">{business?.address}</p>
        </div>
      </div>
      {renderItem && renderItem?.length >= 1 ? <div className="container mt-3">
        <div class="table-responsive">

          <table class="table border-top">
            <tbody>

              {renderItem?.map((item, index) => {
                return <tr key={index}>
                  <td style={{
                    width: '50%'
                  }}>{t(item?.label)}</td>
                  <td>
                    {item.type === 'text' ? business?.[item.key] : ''}
                    {item.type === 'link' ? <a href={business?.[item.key]}>
                      {item.label}
                    </a> : ''}
                    {item.type === 'badge' ? <StatusBadge status={business?.[item.key]}/> : ''}
                    </td>
                </tr>
              })}
            </tbody>
          </table>
        </div>
      </div> : null}

    </div>
  );
}
