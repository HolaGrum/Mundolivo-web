export const WHATSAPP_NUMBER =
  import.meta.env.VITE_WHATSAPP_NUMBER || "+584249322531";

export const whatsappDigits = WHATSAPP_NUMBER.replace(/\D/g, "");
