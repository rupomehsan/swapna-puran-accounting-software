import { createCrudStore } from "@/shared/store/createStore";
import setup from "../setup";

export const transaction_log_store = createCrudStore(setup);
export const store = transaction_log_store;
export default transaction_log_store;
