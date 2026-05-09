import { createCrudStore } from "@/shared/store/createStore";
import setup from "../setup";

export const expense_entry_store = createCrudStore(setup);
export const store = expense_entry_store;
export default expense_entry_store;
