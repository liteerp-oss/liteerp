import React from "react";
import {
  LineChart, Line, XAxis, YAxis, Tooltip, ResponsiveContainer, CartesianGrid,
} from "recharts";
import { useSelector } from "react-redux";
import { useI18n } from "@/i18n/useI18n";

export default function BusinessChart({
  title = "Business Chart",
  data = []
}) {
  const {t} = useI18n();
  const theme = useSelector((state) => state.theme.mode);

  const isDark = theme === "dark-theme";
  const textColor = isDark ? "#e2e8f0" : "#162235";
  const gridColor = isDark ? "#162235" : "#cbd5e1";

  return (
    <div
      className="p-3 rounded-3 mt-4 card-overview"
    >
      <h5 className="mb-3">{title}</h5>
      <ResponsiveContainer width="100%" height={500}>
        <LineChart data={data}>
          <CartesianGrid strokeDasharray="3 3" stroke={gridColor} />
          <XAxis dataKey="name" stroke={textColor} />
          <YAxis stroke={textColor} />
          <Tooltip
            contentStyle={{
              backgroundColor: isDark ? "#1f2b3d" : "#f1f5f9",
              border: "none",
            }}
            labelStyle={{ color: textColor }}
          />

          <Line
            yAxisId="left"
            type="monotone"
            dataKey="revenue"
            name={t("Revenues")}
            stroke="#22c55e"
            strokeWidth={3}
            dot={{ fill: "#22c55e" }}
          />

          <Line
            yAxisId="left"
            type="monotone"
            dataKey="customer"
            name={t("Customers")}
            stroke="#3b82f6"
            strokeWidth={2}
            dot={{ fill: "#3b82f6" }}
          />

          <Line
            yAxisId="left"
            type="monotone"
            dataKey="product"
            name={t("Products")}
            stroke="#a855f7"
            strokeWidth={2}
            dot={{ fill: "#a855f7" }}
          />

          <Line
            yAxisId="left"
            type="monotone"
            dataKey="order"
            name={t("Orders")}
            stroke="#eab308"
            strokeWidth={3}
            dot={{ fill: "#eab308" }}
          />

          <Line
            yAxisId="right"
            type="monotone"
            dataKey="inventory"
            name={t("Inventories")}
            stroke="#db120bff"
            strokeWidth={2}
            dot={{ fill: "#db120bff" }}
          />
          <Line
            yAxisId="right"
            type="monotone"
            dataKey="suppliers"
            name={t("Suppliers")}
            stroke="#09b417ff"
            strokeWidth={2}
            dot={{ fill: "#09b417ff" }}
          />
        </LineChart>
      </ResponsiveContainer>
    </div>
  );
}
