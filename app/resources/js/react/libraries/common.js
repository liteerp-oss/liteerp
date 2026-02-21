export function formatToDateTime(dateStr) {
  const date = new Date(dateStr);
  const Y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  const H = '00';
  const i = '00';
  const s = '00';
  return `${Y}-${m}-${d} ${H}:${i}:${s}`;
}
export function isoToDateTime(isoStr) {
  const date = new Date(isoStr);
  const Y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${Y}-${m}-${d}`;
}
export function substring(data,start = 0,end = 10) {
  return data?.toString()?.length >= 50 ? data?.toString()?.substring(start,end) + '...' : data
}
export function capitalizeFirstLetter(str) {
    if (!str) return "";
    return str.charAt(0).toUpperCase() + str.slice(1);
}