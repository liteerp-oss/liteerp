import { useI18n } from "@/i18n/useI18n";
import React from "react";

const VerticalCommonTable = ({
  data = [],
  title = 'Summary'
}) => {
  const { t } = useI18n();
  return (
    <div>
      <div className='mt-4'>
        <h5 className="fw-bold mb-2 theme-title">{t(title)}</h5>
      </div>
      <table className="table table-bordered rounded-4">
        <tbody>
          {Object.entries(data).map(([key, value]) => (
            <tr key={key}>
              <th className="" style={{ width: "40%" }}>
                {t(key)}
              </th>
              <td>{value}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default VerticalCommonTable;
