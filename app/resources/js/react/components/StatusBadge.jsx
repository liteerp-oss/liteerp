import { useI18n } from "@/i18n/useI18n"
import React from "react"

export default function StatusBadge({
    status = null 
}){
    const {t} = useI18n();
    return <span className={"badge text-uppercase rounded-pill px-3 py-2 badge-" + status}>{t(status)}</span>
}