import { createCrudStore } from "@/shared/store/createStore";
import setup from "../setup";

export const income_entry_store = createCrudStore(setup);
export const store = income_entry_store;
export default income_entry_store;
