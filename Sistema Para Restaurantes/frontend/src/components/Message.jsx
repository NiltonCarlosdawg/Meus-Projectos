import { MdDone, MdError } from "react-icons/md"

const Message = ({type, message}) => {
  return (
    <div className="absolute bottom-3 right-[520px] left-[520px]">
      <div className={type?"border-[2px] border-green-400 py-2 px-8 rounded text-green-900 bg-green-300":"border-[2px] border-red-700 py-2 px-4 rounded text-red-900 bg-red-300"}>
        <div className="flex items-center gap-2 font-bold text-[16px]">
          <span>{type?<MdDone className="text-[30px] font-bold"/>:<MdError className="text-[30px] font-bold"/>}</span> <span>{message}</span>
        </div>
      </div>
    </div>

  )
}

export default Message