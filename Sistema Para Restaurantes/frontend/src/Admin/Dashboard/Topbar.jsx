import { FiCalendar } from "react-icons/fi"

const Topbar = () => {
  let hora = new Date().getHours()
  let user = localStorage.getItem("user").slice(0, 5)
  return (
    <div className="border-b px-4 mt-2 pb-4 border-stone-200 mb-4">
      <div className="flex items-center justify-between p-0.5">
        <div>
          <span className="text-[20px] font-bold block">
            Bem vindo
          </span>
          <span className="text-xs block text-stone-500">{`${new Date().toDateString()}`}</span>
        </div>
        <button className="flex text-sm items-center gap-2 bg-stone-100 transition-colors hover:bg-violet-100 hover:text-violet-700 px-3 py-1.5 rounded">
          <FiCalendar/>
          <span>Prev 6 Months</span>
        </button>
      </div>
    </div>
  )
}

export default Topbar