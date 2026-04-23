type DateLike = string | number | Date | null | undefined

function isValidDate(value: Date) {
  return !Number.isNaN(value.getTime())
}

export function parseDateTime(value: DateLike): Date | null {
  if (!value) return null
  if (value instanceof Date) return isValidDate(value) ? value : null

  if (typeof value === "number") {
    const date = new Date(value)
    return isValidDate(date) ? date : null
  }

  const raw = String(value).trim()
  if (!raw) return null

  // Laravel commonly serializes datetimes as:
  // - ISO 8601 (e.g. 2026-04-23T10:30:00.000000Z)
  // - "YYYY-MM-DD HH:mm:ss" (no timezone, ambiguous in JS)
  const mysqlLike = raw.match(
    /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})(?::(\d{2}))?(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/
  )
  if (mysqlLike) {
    const [, year, month, day, hour, minute, second] = mysqlLike
    const hasTimezone = /Z|[+-]\d{2}:\d{2}$/.test(raw)
    if (!hasTimezone && raw.includes(" ") && !raw.includes("T")) {
      // Treat "YYYY-MM-DD HH:mm:ss" as local time.
      const date = new Date(
        Number(year),
        Number(month) - 1,
        Number(day),
        Number(hour),
        Number(minute),
        Number(second ?? "0")
      )
      return isValidDate(date) ? date : null
    }
  }

  const date = new Date(raw)
  return isValidDate(date) ? date : null
}

export function formatDateDMY(value: DateLike, locale = "en-GB") {
  const date = parseDateTime(value)
  if (!date) return ""
  return date.toLocaleDateString(locale)
}

export function formatTime12h(value: DateLike) {
  const date = parseDateTime(value)
  if (!date) return ""

  const minutes = String(date.getMinutes()).padStart(2, "0")
  let hours = date.getHours()
  const ampm = hours >= 12 ? "PM" : "AM"
  hours = hours % 12
  hours = hours ? hours : 12
  return `${hours}:${minutes} ${ampm}`
}

